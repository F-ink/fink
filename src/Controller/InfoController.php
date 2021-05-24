<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InfoController extends AbstractController
{
    /**
     * @Route("/presentation", name="info")
     */
    public function index(): Response
    {
        return $this->render('info/index.html.twig');
    }

    /**
     * @Route("/mentions-legales", name="mention_légales")
     */
    public function mention(): Response
    {
        return $this->render('info/mention.html.twig');
    }
}
