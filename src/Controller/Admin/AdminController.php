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
     * @Route("/tampon/{id}", name="admin_tampon")
     */
    public function tampon(Artist $artist): Response
    {
        return $this->render('admin/delete.html.twig', [
            'artist' => $artist,
        ]);
    }


     /**
     * @Route("/delete/{id}", name="style_delete")
     */
    public function delete(Artist $artist): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($artist);
        $entityManager->flush();

        return $this->redirectToRoute('admin_');
    }

}
