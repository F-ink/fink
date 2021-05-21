<?php

namespace App\Controller\Admin;

use App\Entity\Artist;
use App\Form\ArtistAdminType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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


        return $this->render('admin/details.html.twig', [
            'artist' => $artist
        ]);
    }

    /**
     * @Route("/admin/add", name="artist_add")
     */

    public function add(Request $request): Response
    {
        $artist = new Artist();
        
        $form = $this->createForm(ArtistAdminType::class, $artist);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

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
     * @Route("update/{id}", name="artist_update")
     */
    public function update(Artist $artist): Response
    {
        if (!empty($_POST)) {

            if (strlen($_POST['lastname']) > 1 && strlen($_POST['firstname']) > 1 ) {

                $entityManager = $this->getDoctrine()->getManager();

                $artist->setEmail($_POST['email']); 
                $artist->setLastname($_POST['lastname']); 
                $artist->setFirstname($_POST['firstname']); 
                $artist->setPseudo($_POST['pseudo']); 
                $artist->setTattooShop($_POST['tattooshop']); 
                $artist->setAddress($_POST['address']); 
                $artist->setProfilePicture($_POST['profilePicture']); 
                $artist->setDescription($_POST['description']); 
                $artist->setInstagram($_POST['instagram']); 
                $artist->setSiret($_POST['siret']); 
                // $artist->addStyle($_POST['style']); 
                
                $entityManager->flush();

                return $this->redirectToRoute('admin_');
            
            }
            
        }
        return $this->render('admin/update.html.twig', [
            'artist' => $artist,
        ]);
    }


    /**
     * @Route("/tampon/{id}", name="artist_tampon")
     */
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

}
