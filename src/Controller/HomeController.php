<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Form\RegistrationFormType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Artist;
use App\Entity\Style;



class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new Artist();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();
        $styles = $entityManager->getRepository(Style::class)->findAll();
        $artistes = $entityManager->getRepository(Artist::class)->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );


            $entityManager->persist($user);
            $entityManager->flush();


            // // generate a signed url and email it to the user
            // $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            //     (new TemplatedEmail())
            //         ->from(new Address('finkart@outlook.fr', 'Fink Art'))
            //         ->to($user->getEmail())
            //         ->subject('Please Confirm your Email')
            //         ->htmlTemplate('registration/confirmation_email.html.twig')
            // );
            // do anything else you need here, like send an email

            return $this->redirectToRoute('_profiler_home');
        }



        return $this->render('home/index.html.twig', [
            'registrationForm' => $form->createView(),
            'styles' => $styles,
            'artistes' => $artistes
        ]);
    }


    /**
     * L'affichage de la page artiste 
     * @Route("/decouvrir/{id}", name="artist_view")
     */
    public function view(int $id): Response
    {
        $em = $this->getDoctrine()->getManager(); // Connexion
        $DetailArtist = $em->getRepository(Artist::class)->find($id); //trouver selon l'ID

        //afficher la page 'detail'
        return $this->render('home/view.html.twig', [
            'DetailArtist' => $DetailArtist
        ]);
    }


    /**
     * @Route("/gallerie", name="gallerie")
     */
    public function gallerie(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new Artist();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();
        $styles = $entityManager->getRepository(Style::class)->findAll();
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );


            $entityManager->persist($user);
            $entityManager->flush();


            // // generate a signed url and email it to the user
            // $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            //     (new TemplatedEmail())
            //         ->from(new Address('finkart@outlook.fr', 'Fink Art'))
            //         ->to($user->getEmail())
            //         ->subject('Please Confirm your Email')
            //         ->htmlTemplate('registration/confirmation_email.html.twig')
            // );
            // do anything else you need here, like send an email

            return $this->redirectToRoute('_profiler_home');
        }


        return $this->render('home/gallerie.html.twig', [
            'registrationForm' => $form->createView(),
            'styles' => $styles
        ]);
    }
}
