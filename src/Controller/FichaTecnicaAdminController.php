<?php

namespace App\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\FichaTecnica;


class FichaTecnicaAdminController extends Controller {

    public function editAction($id = null) {
        $repo = $this->getDoctrine()->getManager()->getRepository(FichaTecnica::class);
        $this->admin->setRepository($repo);

        return parent::editAction($id);
    }

    public function createAction() {
        $repo = $this->getDoctrine()->getManager()->getRepository(FichaTecnica::class);
        $this->admin->setRepository($repo);

        return parent::createAction();
    }

    
    public function MatrizSeguimientoAction() {
        $url = $this->container->get( 'router' )->generate( 'matriz-seguimiento' );
        return new RedirectResponse( $url );
    }
    
    public function batchActionVerFicha($idx = null) {
        $parameterBag = $this->get('request')->request;
        $em = $this->getDoctrine()->getManager();

        $selecciones = $parameterBag->get('idx');
        $fichas = array();
        foreach ($selecciones as $ficha) {
            $fichas[] = $em->find(FichaTecnica::class, $ficha);
        }
        return $this->getFichas($fichas);
    }
}
