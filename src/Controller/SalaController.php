<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\FichaTecnica;
use App\Entity\GrupoIndicadores;

class SalaController extends AbstractController
{
    /**
     * @Route("/tablero/sala/{sala}", name="tablero_sala", options={"expose"=true})
     */
    public function tableroSalaAction($sala, Request $request) {
        $em = $this->getDoctrine()->getManager();

        //$tipo_reporte = ($request->get('indicador') != null) ? 'indicador' : 'sala';

        $html = $this->forward('App\Controller\IndicadorController::tablero', [
            'sala'  => $sala
        ]);
        $html = $html->getContent();

        $info_indicador = '';
        if ($request->get('indicador') != null) {
            //Se está cargando el reporte de la sala como reporte asociado
            //a un indicadores, recuperar el indicador para mostrar
            //información adicional

            $id = $request->get('indicador');
            $indicador = $em->find(FichaTecnica::class, $id);
            $info_indicador .= '<BR/></BR/><BR/></BR/>'
                . '<DIV class="col-md-12" >'
                . '<B>Interpretación:</B><BR/>' . $indicador->getTema()
                . '</DIV><BR/><BR/>'
                . '<DIV class="col-md-12" >'
                . '<B>Concepto:</B></BR>' . $indicador->getConcepto()
                . '</DIV><BR/><BR/>'
                . '<DIV class="col-md-12" >'
                . '<B>Observaciones:</B><BR/>' . $indicador->getObservacion()
                . '</div>'
            ;
        }

        $html = preg_replace("/HTTP.+/", "", $html);
        $html = preg_replace("/Cache.+/", "", $html);
        $html = preg_replace("/Date.+/", "", $html);

        $http = 'http';
        if (array_key_exists('HTTPS', $_SERVER)) {
            $http = ($_SERVER['HTTPS'] == null or $_SERVER['HTTPS'] == 'off') ? 'http' : 'https';
        }
        $html = str_replace(array('href="/bundles', 'src="/bundles', 'src="/app_dev.php'),
            array('href="' . $http . '://' . $_SERVER['HTTP_HOST'] . $this->container->getParameter('directorio') . '/bundles',
                'src="' . $http . '://' . $_SERVER['HTTP_HOST']. $this->container->getParameter('directorio') . '/bundles',
                'src="' . $http . '://'. $_SERVER['HTTP_HOST'] . $this->container->getParameter('directorio') . '/app_dev.php'), $html);
        $html .= $info_indicador;

        try {
            $html = $this->get('knp_snappy.pdf')->getOutputFromHtml($html);
        } catch (\RuntimeException $e) {
            $matches = [];
            if (preg_match('/([^\']+)\'.$/', $e->getMessage(), $matches)) {
                $html = file_get_contents($matches[1]);
                unlink($matches[1]);
            } else  {
                throw $e;
            }
        }

        return new Response(
            $html, 200, array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="reporte.pdf"'
            )
        );
    }

    /**
     * @Route("/sala/{id}/fichas", name="fichas_sala", options={"expose"=true})
     */
    public function fichasSalaAction(GrupoIndicadores $sala) {
        $em = $this->getDoctrine()->getManager();

        $fichas_ = $sala->getIndicadores();
        $fichas = array();
        foreach ($fichas_ as $ficha){
            $fichas[$ficha->getIndicador()->getId()] = $ficha->getIndicador();
        }

        return $this->getFichas($fichas);
    }

    protected function getFichas($fichas){
        $salida = '';
        foreach ($fichas as $ficha) {

            $admin = $this->get('sonata.admin.ficha');
            $admin->setSubject($ficha);

            $html = $this->render($admin->getTemplate('show'), array(
                'action' => 'show',
                'object' => $ficha,
                'elements' => $admin->getShow(),
                'admin' => $admin,
                'base_template' => 'ajax_layout.html.twig'
            ));

            $salida .= $html->getContent() . '<BR /><BR />';
        }
        //Quitar los comentarios del código html, enlaces y aplicar estilos
        $salida = preg_replace('/<!--(.|\s)*?-->/', '', $salida);
        $salida = preg_replace('/<a(.|\s)*?>/', '', $salida);
        $salida = str_ireplace('</a>', '', $salida);
        $salida = str_ireplace('TD', "TD STYLE='border: 2px double black'", $salida);
        $salida = str_ireplace('TH', "TH STYLE='border: 2px double black'", $salida);
        $salida = str_ireplace('<TABLE', "<TABLE width=95% ", $salida);

        return new Response('<HTML>' . $salida . '</HTML>', 200, array(
                'Content-Type' => 'application/msword; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="fichas_tecnicas.doc"',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            )
        );
    }

    /**
     * @Route("/sala/tablas-datos", name="tablas_datos_sala", options={"expose"=true})
     */
    public function tablasDatosSalaAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $req = $request;

        $titulos = json_decode($req->get('titulos'), true);
        $tablas = json_decode($req->get('tablas'), true);

        $html = '<HTML><HEAD><meta http-equiv="content-type" content="text/html; charset=UTF-8" /><STYLE>table{border-collapse: collapse } td, th {border: 1px solid black} </STYLE></HEAD><BODY>';

        foreach ($titulos as $k=> $t){
            $html .= '<h3>'.$t.'</h3><BR/>'.$tablas[$k].'<BR/><BR/>';
        }
        $html .= '</body></html>';

        return new Response($html, 200, array(
                'Content-Type' => 'data:application/vnd.ms-excel;base64',
                'Content-Disposition' => 'attachment; filename="tablas_datos.xls"',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            )
        );
    }
}
