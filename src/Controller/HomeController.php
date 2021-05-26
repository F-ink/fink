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

     /**
     * @Route("/result", name="result")
     */
    public function search(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $artiste = $entityManager->getRepository(Artist::class)->findBy(['city' => $_POST['city']]);

        $styles = $entityManager->getRepository(Style::class)->findAll();
        
        return $this->redirectToRoute('result');

        return $this->render('home/result.html.twig', [
            'artiste' => $artiste,
            'styles' => $styles
        ]);
    } 
}


// class GmapApi {

//     private static $apikey = 'AIzaSyAKqH7OKs53L9sM0IUAc5UJpxtRyNz2GMo';

//     public static function geocodeAddress($address) {
//         //valeurs vide par défaut
//         $data = array('address' => '', 'lat' => '', 'lng' => '', 'city' => '', 'department' => '', 'region' => '', 'country' => '', 'postal_code' => '');
//         //on formate l'adresse
//         $address = str_replace(" ", "+", $address);
//         //on fait l'appel à l'API google map pour géocoder cette adresse
//         $json = file_get_contents("https://maps.google.com/maps/api/geocode/json?key=" . self::$apikey . "&address=$address&sensor=false&region=fr");
//         $json = json_decode($json);
//         //on enregistre les résultats recherchés
//         if ($json->status == 'OK' && count($json->results) > 0) {
//             $res = $json->results[0];
//             //adresse complète et latitude/longitude
//             $data['address'] = $res->formatted_address;
//             $data['lat'] = $res->geometry->location->lat;
//             $data['lng'] = $res->geometry->location->lng;
//             foreach ($res->address_components as $component) {
//                 //ville
//                 if ($component->types[0] == 'locality') {
//                     $data['city'] = $component->long_name;
//                 }
//                 //départment
//                 if ($component->types[0] == 'administrative_area_level_2') {
//                     $data['department'] = $component->long_name;
//                 }
//                 //région
//                 if ($component->types[0] == 'administrative_area_level_1') {
//                     $data['region'] = $component->long_name;
//                 }
//                 //pays
//                 if ($component->types[0] == 'country') {
//                     $data['country'] = $component->long_name;
//                 }
//                 //code postal
//                 if ($component->types[0] == 'postal_code') {
//                     $data['postal_code'] = $component->long_name;
//                 }
//             }
//         }
//         return $data;
//     }

// }



// $geocoder = new \OpenCage\Geocoder\Geocoder('89c46309bda04d75a85786956180d2cb');
//         $geoResult = $geocoder->geocode($form->get('city')->getData());
//         if ($geoResult && $geoResult['total_results'] > 0) {
//             $first = $geoResult['results'][0];
//         dd($first);

//         $geopoints = [
//             'lng' => $first['geometry']['lng'],
//             'lat' => $first['geometry']['lat'],
//         ];
//         dd($geopoints);
//     }