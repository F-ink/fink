<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Repository\ArtistRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, MailerInterface $mailer): Response
    {
        $user = new Artist();
        $user->setCreatedAt(new \DateTime('now'));
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // Formulaire de connexion
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )

            );
            
            $user->setRoles(['ROLE_USER']);
            $user->setActivationToken(md5(uniqid()));
            //$user->setCreatedAt(new \DateTime('now'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            // On crée le message
            $message = (new Email())
                // On attribue l'expéditeur
                ->From('finkart@outlook.fr')
                // On attribue le destinataire
                ->To($user->getEmail())
                // On crée le texte avec la vue
                ->subject('Time to activate your account')

                ->html(
                    $this->renderView(
                        'emails/activation.html.twig',
                        ['token' => $user->getActivationToken()]
                    ),
                    'text/html'
                );
            $mailer->send($message);

            $this->addFlash('message', 'Merci pour votre inscription. Un mail d\'activation vous a été envoyé! ');
            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    /**
     * @Route("/activation/{token}", name="activation")
     */
    public function activation($token, ArtistRepository $users)
    {
        // On recherche si un utilisateur avec ce token existe dans la base de données
        $user = $users->findOneBy(['activation_token' => $token]);

        // Si aucun utilisateur n'est associé à ce token
        if (!$user) {
            // On renvoie une erreur 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        // On supprime le token
        $user->setActivationToken(null);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // On génère un message
        $this->addFlash('message', 'Votre compte a été activé avec succès, connectez-vous pour compléter votre profil artiste');

        // On retourne à l'accueil
        return $this->redirectToRoute('home');
    }
}
