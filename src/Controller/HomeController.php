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
use Doctrine\ORM\Mapping\Id;

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
        
        //j'affiche les nouveaux inscrits = par ordre d'ID, le plus grand ID est donc le dernier: limité à 4 affichages
        $artistes = $entityManager->getRepository(Artist::class)->findBy(
            array(),
            array('id' => 'DESC'), 4);
       

        // Formulaire de connexion
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
           
            // generate a signed url and email it to the user
            // $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            //     (new TemplatedEmail())
            //         ->from(new Address('finkart@outlook.fr', 'Fink Art'))
            //         ->to($user->getEmail())
            //         ->subject('Please Confirm your Email')
            //         ->htmlTemplate('registration/confirmation_email.html.twig')
            // );
            // do anything else you need here, like send an email

            return $this->redirectToRoute('home');
        }



        return $this->render('home/index.html.twig', [
            'registrationForm' => $form->createView(),
            'styles' => $styles,
            'artistes' => $artistes
            // 'artist_style' =>$artist_style
        ]);
    }


    /**
     * L'affichage de la page artiste en détail
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
     * *Affichage de tous les artistes de la bdd dont le profil est validé par l'admin et publié sur le site, dans l'ordre de création (recent->old)
     * @Route("/gallerie", name="gallerie")
     */
    public function gallerie(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new Artist();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();
        $styles = $entityManager->getRepository(Style::class)->findAll();
        
        //j'affiche les artistes  - de la date de création la plus récent vers la plus vieille avec l'id en AI, same result
        $artistes = $entityManager->getRepository(Artist::class)->findBy(
            array(),
            array('id' => 'DESC')
        );

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

            return $this->redirectToRoute('home');
        }


        return $this->render('home/gallerie.html.twig', [
            'registrationForm' => $form->createView(),
            'styles' => $styles,
            'artistes' => $artistes
            
        ]);
    }
}
