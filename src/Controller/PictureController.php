<?php

namespace App\Controller;
use App\Entity\Artist;
use App\Entity\Picture;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PictureController extends AbstractController
{
    /**
     * @Route("/picture/add", name="picture")
     */
    public function add(int $id): Response
    {    $errors = [];

        $em = $this->getDoctrine()->getManager(); // Connexion
        $artist = $em->getRepository(Artist::class)->find($id);
        $picture = new Picture;
        








        return $this->render('picture/index.html.twig', [
            'controller_name' => 'PictureController',
        ]);
    }
}
