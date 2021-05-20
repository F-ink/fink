<?php

namespace App\Controller\Admin;

use App\Repository\StyleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Style;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\StylesType;

/**
     * @Route("/admin/styles", name="admin_styles_")
     * @package App\Controller\Admin
     */

class StylesController extends AbstractController
{
    /**
     * @Route("/", name="home_styles")
     */
    public function index(StyleRepository $stylesRepository): Response
    {
        return $this->render('admin/styles/index.html.twig', [
            'styles' => $stylesRepository->findAll()
        ]);
    }

    /**
     * @Route("/ajout", name="ajout")
     */
    public function ajout(Request $request): Response
    {
        $style = new Style();
        
        $form = $this->createForm(StylesType::class, $style);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $em->persist($style);
                $em->flush();

                return $this->redirectToRoute('admin_styles_home_styles');
            
        }
            
        return $this->render('admin/styles/ajout.html.twig', [
                'form' => $form->createView(),
        ]);
    }
}

