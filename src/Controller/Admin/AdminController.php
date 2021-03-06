<?php

namespace App\Controller\Admin;

use App\Entity\Artist;
use App\Entity\Style;

use App\Form\ArtistAdminType;
use App\Repository\StyleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $artists = $em->getRepository(Artist::class)->findAll();

        return $this->render('admin/index.html.twig', [
            'artists' => $artists,
        ]);
    }

    /**
     * @Route("/admin/details/{id}}", name="admin_details")
     */
    public function details(int $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $artist = $em->getRepository(Artist::class)->find($id);
        $artist_style = $artist->getStyles();


        return $this->render('admin/details.html.twig', [
            'artist' => $artist,
            'artist_style' => $artist_style

        ]);
    }

    /**
     * @Route("/admin/add", name="artist_add")
     */
    // Ajout d'un artiste
    public function add(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $artist = new Artist();

        $form = $this->createForm(ArtistAdminType::class, $artist);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $artist->setPassword(
                $passwordEncoder->encodePassword(
                    $artist,
                    $form->get('password')->getData()
                )
                
            );

            $geocoder = new \OpenCage\Geocoder\Geocoder('89c46309bda04d75a85786956180d2cb');
            $geoResult = $geocoder->geocode($form->get('address')->getData() . ' ' . $form->get('city')->getData());
            if ($geoResult && $geoResult['total_results'] > 0) {
                $first = $geoResult['results'][0];
                // dd($first);

                $geopoints = [
                    'lng' => $first['geometry']['lng'],
                    'lat' => $first['geometry']['lat'],
                ];

                $artist->setLat($geopoints['lat']);
                $artist->setLng($geopoints['lng']);
                // dd($geopoints);
            }
                
            $profilePicture = $form->get('profilePicture')->getData();

            $file = md5(uniqid()) . '.' . $profilePicture->guessExtension();

            $profilePicture->move(
                $this->getParameter('images_directory'),
                $file
            );

                $artist->setProfilePicture($file);

            $coverPicture = $form->get('coverPicture')->getData();

            $file = md5(uniqid()) . '.' . $coverPicture->guessExtension();

            $coverPicture->move(
                $this->getParameter('cover_directory'),
                $file
            );

                $artist->setCoverPicture($file);

                $em = $this->getDoctrine()->getManager();
                $em->persist($artist);
                $em->flush();

                return $this->redirectToRoute('admin_');
            }

            return $this->render('admin/add.html.twig', [
                'form' => $form->createView(),
            ]);
        }


    /**
     * @Route("/admin/update/{id}", name="artist_update")
     */

    // Mise ?? jour de l'artiste
    public function update(Artist $artist, int $id): Response
    {

        if (!empty($_POST)) {

            if (strlen($_POST['lastname']) > 1 && strlen($_POST['firstname']) > 1) {

                $entityManager = $this->getDoctrine()->getManager();

                $artist->setEmail($_POST['email']);
                $artist->setLastname($_POST['lastname']);
                $artist->setFirstname($_POST['firstname']);
                $artist->setPseudo($_POST['pseudo']);
                $artist->setTattooShop($_POST['tattooshop']);
                $artist->setCity($_POST['city']);
                $artist->setAddress($_POST['address']);
                $artist->setProfilePicture($_POST['profilePicture']);
                $artist->setCoverPicture($_POST['coverPicture']);
                $artist->setDescription($_POST['description']);
                $artist->setInstagram($_POST['instagram']);
                $artist->setSiret($_POST['siret']);
                // $artist->addStyle($_POST['style']);
                
                $geocoder = new \OpenCage\Geocoder\Geocoder('89c46309bda04d75a85786956180d2cb');
                $geoResult = $geocoder->geocode($_POST['address'] . ' ' . $_POST['city']);
                if ($geoResult && $geoResult['total_results'] > 0) {
                    $first = $geoResult['results'][0];
                    // dd($first);
    
                    $geopoints = [
                        'lng' => $first['geometry']['lng'],
                        'lat' => $first['geometry']['lat'],
                    ];
    
                    $artist->setLat($geopoints['lat']);
                    $artist->setLng($geopoints['lng']);
                    // dd($geopoints);
                }
                $entityManager->flush();

                return $this->redirectToRoute('admin_');
            }
        }
        return $this->render('admin/update.html.twig', [
            'artist' => $artist

        ]);
    }


    /**
     * @Route("/admin/active/{id}", name="artist_active")
     */

    //  Activation dans le l'artiste dans la base de donn??es
    public function active(Artist $artist)
    {
        $artist->setIsVerified(($artist->getIsVerified()) ? false : true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($artist);
        $em->flush();

        return new Response('true');
    }


    /**
     * @Route("/tampon/{id}", name="artist_tampon")
     */

    // Page tampon pour suppression
    public function tampon(Artist $artist): Response
    {
        return $this->render('admin/delete.html.twig', [
            'artist' => $artist,
        ]);
    }


    /**
     * @Route("/delete/{id}", name="artist_delete")
     */
    public function delete(Artist $artist): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($artist);
        $entityManager->flush();

        return $this->redirectToRoute('admin_');
    }


    // public function style(StyleRepository $stylesRepository): Response
    // {
    //     return $this->render('admin/update.html.twig', [
    //         'styles' => $stylesRepository->findAll()
    //     ]);
    // }
}
