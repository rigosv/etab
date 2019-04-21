<?php

namespace App\Controller;

use App\Entity\ConfiguracionPivotTable;
use MINSAL\Bundle\CalidadBundle\Entity\Estandar;
use MINSAL\Bundle\CalidadBundle\Entity\Indicador;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\FichaTecnica;
use App\Entity\Bitacora;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PivotTableController
 * @package App\Controller
 *
 * @Route("/pivottable")
 */

class PivotTableController extends AbstractController {


    /**
     * @return Response
     *
     * @Route("/index", name="pivotTable")
     */

    public function PivotTableAction() {
        $em = $this->getDoctrine()->getManager();
        $usuario = $this->getUser();

        $datos = $em->getRepository(FichaTecnica::class)->getListadoIndicadores($usuario);

        $formularios = array();

        $MINSALCalidadBundle = ['habilitado' => false, 'estandares'=> ['pna' => [], 'hosp'=> []] ];

        if (array_key_exists('CalidadBundle' , $this->getParameter('kernel.bundles'))
            and ( $usuario->hasRole('ROLE_SUPER_ADMIN') or $usuario->hasRole('ROLE_USER_TABLERO_CALIDAD')) ) {

            $MINSALCalidadBundle['habilitado'] = true;
            //Recuperar los formularios
            $MINSALCalidadBundle['estandares']['pna'] = $em->getRepository(Indicador::class)->getIndicadoresEvaluadosListaChequeoNivel('pna');
            $MINSALCalidadBundle['estandares']['hosp'] = $em->getRepository(Indicador::class)->getIndicadoresEvaluadosListaChequeoNivel('hosp');
        }

        return $this->render('PivotTable/index.html.twig', array(
            'categorias' => $datos['categorias'],
            'clasificacionUso' => $datos['clasficacion_uso'],
            'indicadores_no_clasificados' => $datos['indicadores_no_clasificados'],
            'formularios' => $formularios,
            'MINSALCalidadBundle' => $MINSALCalidadBundle
        ));
    }


    /**
     * @Route("/guardar_estado/", name="pivotable_guardar_estado", options={"expose"=true})
     */
    public function guardarEstadoAction(Request $request, TranslatorInterface $translator) {
        $em = $this->getDoctrine()->getManager();
        $response = new Response();

        $idElemento = $request->get('id');
        $tipoElemento = $request->get('tipoElemento');
        $nombreEscenario = $request->get('nombre');
        $esPorDefecto = $request->get('pordefecto') == 'true';
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $usuarioId = $usuario->getId();

        //json_encode sobre json_decode, quita los saltos de línea que trae configuración
        $configuracion = json_encode(json_decode($request->get('configuracion')));

        //verificar si ya existe para ese elemento un escenario con ese nombre
        $escenario = $em->getRepository(ConfiguracionPivotTable::class)->findOneBy([
                                'tipoElemento' => $tipoElemento,
                                'idElemento' => $idElemento,
                                'nombre' => $nombreEscenario
                            ]);
        //Si no existe el escenario, crearlo
        if ( !$escenario ) {
            $escenario = new ConfiguracionPivotTable();
            $escenario->setTipoElemento( $tipoElemento );
            $escenario->setIdElemento( $idElemento );
            $escenario->setNombre( $nombreEscenario );
            $escenario->setUsuario( $usuario );
        }

        //Si no es el usuario dueño, no permitir el cambio de configuración
        if ( $escenario->getUsuario()->getId() != $usuarioId ){
            return new JsonResponse(['estado'=>'error', 'mensaje' => $translator->trans('_no_puede_sobreescribir_configuracion_escenario_otro_usuario_')]);
        }


        //Si cambió a ser por defecto, poner todos los demás escenarios de ese elemento en por defecto en falso
        if ($esPorDefecto and $escenario->isPorDefecto() == false ) {
            $escenarios = $em->getRepository(ConfiguracionPivotTable::class)->findBy([
                'tipoElemento' => $tipoElemento,
                'idElemento' => $idElemento,
                'porDefecto' => true
            ]);

            foreach ( $escenarios as $e ) {
                $e->setPorDefecto(false);
                $em->persist($e);
            }
        }

        $escenario->setConfiguracion($configuracion);
        $escenario->setPorDefecto($esPorDefecto);
        $em->persist($escenario);
        $em->flush();

        return new JsonResponse(['estado'=>'success', 'mensaje' => $translator->trans('_escenario_guardado_correctamente_')]);
    }

    /**
     * @Route("/obtener_estado/", name="pivotable_obtener_estado", options={"expose"=true})
     */
    public function obtenerEstadoAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $response = new Response();

        $tipoElemento = $request->get('tipoElemento');
        $idElemento = $request->get('id');
        $usuario = $this->get('security.token_storage')->getToken()->getUser();

        $conf = $em->getRepository(FichaTecnica::class)->getConfPivoTable($tipoElemento, $idElemento, $usuario->getId());
        //$conf = str_replace('\\\\', '', $conf['configuracion']);
        $response->setContent($conf['configuracion']);
        return $response;
    }


    /**
     * @Route("/get_escenarios/{id}", name="pivotable_get_escenarios", options={"expose"=true})
     */
    public function getEscenarios(FichaTecnica $indicador, Request $request) {
        $em = $this->getDoctrine()->getManager();

        return $this->render('PivotTable/guardar_escenario.html.twig', [
            'indicador' => $indicador
        ]);
    }

    /**
     * @Route("/datos/{id}", name="get_datos_evaluacion_calidad", options={"expose"=true})
     */
    public function getDatosEvaluacionCalidadAction($id) {
        $em = $this->getDoctrine()->getManager();
        $response = new Response();

        $datos = $em->getRepository(Estandar::class)->getDatosCalidad($id);

        $response->setContent(json_encode($datos));
        return $response;
    }

    /**
     * @Route("/logactividad/", name="get_log_actividad", options={"expose"=true})
     */
    public function getLogActividadAction() {
        $em = $this->getDoctrine()->getManager();
        $response = new Response();

        $datos = $em->getRepository(Bitacora::class)->getLog();

        $response->setContent(json_encode($datos));
        return $response;
    }

    /**
     * @Route("/pivotable/reporte-expediente-p/", name="get_reporte_expedientes_evaluacion_calidad_p", options={"expose"=true})
     */
    public function getReporteExpedientesEvaluacionCalidadP() {
        $em = $this->getDoctrine()->getManager();
        $response = new Response();

        $datos = $em->getRepository(Indicador::class)->getDatosCalidad();

        $response->setContent(json_encode($datos));
        return $response;
    }
    /**
     * @Route("/pivotable/reporte-expediente-h/", name="get_reporte_expedientes_evaluacion_calidad_h", options={"expose"=true})
     */
    public function getReporteExpedientesEvaluacionCalidadH() {
        $em = $this->getDoctrine()->getManager();
        $response = new Response();

        $datos = $em->getRepository(Indicador::class)->getDatosCalidad();

        $response->setContent(json_encode($datos));
        return $response;
    }


}