<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InfoController extends AbstractController
{
    // PAGE PRESENTATION DU SUJET 
    /**
     * @Route("/presentation", name="info")
     */
    public function index(): Response
    {
        return $this->render('info/index.html.twig');
    }


    // PAGE MENTIONS LEGALES + CREDITS
    /**
     * @Route("/mentions-legales", name="mention_légales")
     */
    public function mention(): Response
    {
        return $this->render('info/mention.html.twig');
    }


    // ICI LA PAGE D'INFORMATIONS POUR LE COMPTE PREMIUM
    /**
     * @Route("/forfaits-fink", name="premium")
     */
    public function premium(): Response
    {
        return $this->render('info/premium.html.twig');
    }
}
