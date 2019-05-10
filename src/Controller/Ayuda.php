<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\FichaTecnica;
use App\Entity\GrupoIndicadores;

class Ayuda extends AbstractController
{
    /**
     * @Route("/ayuda/", name="ayuda", options={"expose"=true})
     */
    public function index() {
        return $this->render('Ayuda/index.html.twig');
    }
}
