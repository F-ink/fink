<?php

namespace App\Controller\Admin;

use App\Repository\StyleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Style;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\StylesAdminType;

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
     * @Route("/add", name="add")
     */
    public function add(Request $request): Response
    {
        $style = new Style();
        
        $form = $this->createForm(StylesAdminType::class, $style);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $em->persist($style);
                $em->flush();

                return $this->redirectToRoute('admin_styles_home_styles');
            
        }
            
        return $this->render('admin/styles/add.html.twig', [
                'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/update/{id}", name="style_update")
     */
    public function update(Style $style): Response
    {
        if (!empty($_POST)) {

            if (strlen($_POST['name']) > 5) {

                $entityManager = $this->getDoctrine()->getManager();

                $style->setName($_POST['name']); 
                
                $entityManager->flush();

                return $this->redirectToRoute('admin_styles_home_styles');
            
            }
            
        }
        return $this->render('admin/styles/update.html.twig', [
            'style' => $style,
        ]);
    }



    /**
     * @Route("/tampon/{id}", name="style_tampon")
     */

      // Page tampon pour suppression
    public function tampon(Style $style): Response
    {
        return $this->render('admin/styles/delete.html.twig', [
            'style' => $style,
        ]);
    }


     /**
     * @Route("/delete/{id}", name="style_delete")
     */
    public function delete(Style $style): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($style);
        $entityManager->flush();

        return $this->redirectToRoute('admin_styles_home_styles');
    }
}

