<?php

namespace App\Controller;

use App\Repository\ArtistRepository;
use App\Entity\Artist;
use App\Form\ResetPassType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/forgot-pass", name="app_forgotten_password")
     * 
     */

    public function forgottenPass(
        Request $request,
        ArtistRepository $users,
        MailerInterface $mailer,
        TokenGeneratorInterface $tokenGenerator
    ): Response {
        // On initialise le formulaire
        $form = $this->createForm(ResetPassType::class);

        // On traite le formulaire
        $form->handleRequest($request);

        // Si le formulaire est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On r??cup??re les donn??es
            $donnees = $form->getData();

            // On cherche un utilisateur ayant cet e-mail
            $user = $users->findOneByEmail($donnees['email']);

            // Si l'utilisateur n'existe pas
            if ($user === null) {
                // On envoie une alerte disant que l'adresse e-mail est inconnue
                $this->addFlash('danger', 'Cette adresse e-mail est inconnue');

                // On retourne sur la page de connexion
                return $this->redirectToRoute('app_login');
            }

            // On g??n??re un token
            $token = $tokenGenerator->generateToken();

            // On essaie d'??crire le token en base de donn??es
            try {
                $user->setResetToken($token);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', 'Une erreur est survenue : ' . $e->getMessage());
                return $this->redirectToRoute('home');
            }

            // On g??n??re l'URL de r??initialisation de mot de passe
            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            // On g??n??re l'e-mail
            $message = (new Email())
                ->From('no-reply@finkart.fr')
                ->To($user->getEmail())
                ->subject('Fink: r??initialisation de votre mot de passe')
                ->html(
                    "<p>Bonjour</p><p>Une demande de r??initialisation de mot de passe a ??t?? effectu??e pour le site FinkArt.fr. Veuillez cliquer sur le lien suivant : <a href='" . $url . "'>Reinitialisation</a></p><p>Attention ,il n'est valable que 15 minutes ! </p>",
                    'text/html'
                );

            // On envoie l'e-mail
            $mailer->send($message);

            // On cr??e le message flash de confirmation
            $this->addFlash('message', 'E-mail de r??initialisation du mot de passe envoy?? !');

            // On redirige vers la page de login
            return $this->redirectToRoute('home');
        }

        // On envoie le formulaire ?? la vue
        return $this->render('security/forgotten_password.html.twig', ['emailForm' => $form->createView()]);
    }


    /**
     * @Route("/reset_pass/{token}", name="app_reset_password")
     */
    public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder)
    {
        // On cherche un utilisateur avec le token donn??
        $user = $this->getDoctrine()->getRepository(Artist::class)->findOneBy(['reset_token' => $token]);

        // Si l'utilisateur n'existe pas
        if ($user === null) {
            // On affiche une erreur
            $this->addFlash('danger', 'Token Inconnu');
            return $this->redirectToRoute('home');
        }

        // Si le formulaire est envoy?? en m??thode post
        if ($request->isMethod('POST')) {
            // On supprime le token
            $user->setResetToken(null);

            // On chiffre le mot de passe
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));

            // On stocke
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // On cr??e le message flash
            $this->addFlash('message', 'Mot de passe mis ?? jour');

            // On redirige vers la page de connexion
            return $this->redirectToRoute('home');
        } else {
            // Si on n'a pas re??u les donn??es, on affiche le formulaire
            return $this->render('security/reset_password.html.twig', ['token' => $token]);
        }
    }
}
