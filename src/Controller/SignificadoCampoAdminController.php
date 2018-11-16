<?php

namespace App\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use App\Entity\OrigenDatos;

class SignificadoCampoAdminController extends Controller
{
    public function editAction($id = null)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(OrigenDatos::class);
        $this->admin->setRepository($repo);

        return parent::editAction($id);

    }

    public function createAction()
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(OrigenDatos::class);
        $this->admin->setRepository($repo);

        return parent::createAction();

    }
}
