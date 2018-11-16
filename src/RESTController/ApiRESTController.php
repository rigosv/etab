<?php

namespace App\RESTController;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\FichaTecnica;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Entity\Agencia;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;


class ApiRESTController extends Controller {

    /**
     * Obtener los datos de todos los indicadores asignados a una agencia
     *
     * @Rest\Get("/api/{cod_agencia}/indicadores/data", name="api_get_indicadores", options={"expose"=true})
     * @Rest\View
     *
     * @SWG\Response(
     *     response=200,
     *     description="Retorna el listado de los indicadores",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref="#/definitions/DatosIndicador")
     *     )
     * )
     *
     * @SWG\Parameter(
     *     name="cod_agencia",
     *     in="path",
     *     type="string",
     *     description="Código de la agencia de la que se quiere recuperar el listado de indicadores"
     * )
     *
     * @SWG\Tag(name="Indicadores")
     */
    public function getIndicadoresAction($cod_agencia) {
        $em = $this->getDoctrine()->getManager();

        $response = new Response();
        $respuesta = array('state' => 'ok');

        $agencia = $em->getRepository(Agencia::class)->findOneBy(array('codigo' => $cod_agencia));

        if ($agencia !== null) {

            //$fichaRepository = $em->getRepository(FichaTecnica::class);
            $indicadores = $agencia->getIndicadores();

            $respuesta['datos'] = $this->getDatosIndicador($indicadores);
        } else {
            $respuesta = array('state' => 'fail', 'message' => 'Agencia no existe');
        }
        $response->setContent(json_encode($respuesta));
        return $response;
    }

    /**
     * Obtener los datos de un indicador asignado a una agencia
     *

     * @REST\Get("/api/{cod_agencia}/indicador/{id}/data", name="api_get_indicador", options={"expose"=true})
     * @Rest\View
     *
     * @SWG\Response(
     *     response=200,
     *     description="Retorna los datos de un indicador",
     *     @SWG\Schema(ref="#/definitions/DatosIndicador")
     * )
     *
     * @SWG\Parameter(
     *     name="cod_agencia",
     *     in="path",
     *     type="string",
     *     description="Código de la agencia de la que se quiere recuperar el listado de indicadores"
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="El número identificador del indicador"
     * )
     *
     * @SWG\Tag(name="Indicadores")
     */
    public function getIndicadorAction($cod_agencia, $id) {
        $em = $this->getDoctrine()->getManager();

        $response = new Response();
        $respuesta = array('state' => 'ok');

        $fichaRepository = $em->getRepository(FichaTecnica::class);

        $indicador = $fichaRepository->find($id);

        if ($indicador !== null) {
            $agencias = $indicador->getAgenciasAcceso();

            //Verificar si el indicador pertenece a la agencia especificada
            $agencia_ = false;
            foreach ($agencias as $agencia) {
                if ( $agencia->getCodigo() == $cod_agencia ){
                    $agencia_ = true;
                }
            }

            if ( $agencia_ ){
                $indicadores[] = $indicador;
                $respuesta['datos'] = $this->getDatosIndicador($indicadores);
            } else {
                $respuesta = array('state' => 'fail', 'message' => 'Indicador no está asignado a la agencia especificada');
            }

        } else {
            $respuesta = array('state' => 'fail', 'message' => 'Indicador no existe');
        }
        $response->setContent(json_encode($respuesta));
        return $response;
    }

    protected function getDatosIndicador($indicadores) {
        $em = $this->getDoctrine()->getManager();

        $fichaRepository = $em->getRepository(FichaTecnica::class);
        $datos = array();

        foreach ($indicadores as $ind) {
            try {
                $fichaRepository->crearIndicador($ind);
                $datosIndicador = $fichaRepository->getDatosIndicador($ind);
                $datos[] = array('indicador_id' => $ind->getId(), 'nombre' => $ind->getNombre(),
                    'formula' => str_replace(array('{', '}'), array('__', '__'), strtolower($ind->getFormula())),
                    'filas' => $datosIndicador
                );
            } catch (Exception $exc) {
                $respuesta = array('state' => 'fail', 'message' => $exc->getTraceAsString());
                $response->setContent(json_encode($respuesta));
                return $response;
            }
        }

        return $datos;
    }

    /**
     * Obtener listado de fichas, id y nombre
     *
     * @Rest\Get("/api/{cod_agencia}/fichastecnicas/listado", name="api_get_fichas_listado", options={"expose"=true})
     * @Rest\View
     *
     * @SWG\Response(
     *     response=200,
     *     description="Retorna el listado de fichas técnicas disponibles para una agencia",
     *     examples={{
     *              {"id_ficha": "34", "nombre": "Porcentaje de muertes"},
     *              {"id_ficha": "334", "nombre": "Porcentaje de casos atendidos"}
     *          }},
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(
     *              type="object",
     *              @SWG\Property(property="id_ficha", type="integer", example=34),
     *              @SWG\Property(property="nombre", type="string", example="Porcentaje de muertes")
     *          )
     *     )
     * )
     *
     * @SWG\Parameter(
     *     name="cod_agencia",
     *     in="path",
     *     type="string",
     *     description="Código de la agencia de la que se quiere recuperar el listado de indicadores"
     * )
     *
     * @SWG\Tag(name="Indicadores")
     *
     */
    public function getFichasListadoAction($cod_agencia) {
        $em = $this->getDoctrine()->getManager();

        $agencia = $em->getRepository(Agencia::class)->findOneBy(array('codigo' => $cod_agencia));

        $response = new Response();

        $respuesta = array('state' => 'ok');
        $datos = array();
        $fichaRepository = $em->getRepository(FichaTecnica::class);

        foreach ($agencia->getIndicadores() as $ind) {
            try {
                $datos[] = array('id_ficha' => $ind->getId(), 'nombre' => $ind->getNombre());
            } catch (Exception $exc) {
                $respuesta = array('state' => 'fail', 'message' => $exc->getTraceAsString());
                $response->setContent(json_encode($respuesta));
                return $response;
            }
        }
        $respuesta['datos'] = $datos;
        $response->setContent(json_encode($respuesta));
        return $response;
    }

    /**
     * Obtener campos descriptores de las fichas técnicas
     *
     * @Rest\Get("/api/{cod_agencia}/fichastecnicas", name="api_get_fichas", options={"expose"=true})
     * @Rest\View
     *
     * @SWG\Response(
     *     response=200,
     *     description="Retorna cada ficha técnica con su información",
     *     @SWG\Schema(ref="#/definitions/FichaTecnica")
     * )
     *
     * @SWG\Parameter(
     *     name="cod_agencia",
     *     in="path",
     *     type="string",
     *     description="Código de la agencia de la que se quiere recuperar el listado de indicadores"
     * )
     *
     * @SWG\Tag(name="Indicadores")
     *
     */
    public function getFichasAction($cod_agencia) {
        $em = $this->getDoctrine()->getManager();

        $agencia = $em->getRepository(Agencia::class)->findOneBy(array('codigo' => $cod_agencia));

        $response = new Response();

        $respuesta = array('state' => 'ok');
        $datos = array();
        $fichaRepository = $em->getRepository(FichaTecnica::class);

        foreach ($agencia->getIndicadores() as $ind) {
            try {
                $clasificacionTecnica = array();
                foreach ($ind->getClasificacionTecnica() as $c) {
                    $clasificacionTecnica[] = $c->getDescripcion();
                }
                $variables = array();
                foreach ($ind->getVariables() as $v) {
                    $variables[] = $v->getIniciales();
                }
                $alertas = array();
                foreach ($ind->getAlertas() as $a) {
                    $alertas[] = array('limite_inferior' => $a->getLimiteInferior(),
                        'limite_superior' => $a->getLimiteSuperior(),
                        'color' => $a->getColor()->getColor()
                    );
                }
                $datos[] = array('id_ficha' => $ind->getId(),
                    'nombre' => $ind->getNombre(),
                    'interpretacion' => $ind->getTema(),
                    'concepto' => $ind->getConcepto(),
                    'unidad_medida' => $ind->getUnidadMedida(),
                    'formula' => $ind->getFormula(),
                    'observacion' => $ind->getObservacion(),
                    'campos' => $ind->getCamposIndicador(),
                    'clasificacion_tecnica' => $clasificacionTecnica,
                    'meta' => $ind->getMeta(),
                    'variables' => $variables,
                    'alertas' => $alertas
                );
            } catch (Exception $exc) {
                $respuesta = array('state' => 'fail', 'message' => $exc->getTraceAsString());
                $response->setContent(json_encode($respuesta));
                return $response;
            }
        }
        $respuesta['datos'] = $datos;
        $response->setContent(json_encode($respuesta));
        return $response;
    }

    /**
     * Obtener los datos de los formularios asignados a una agencia
     * @param string $cod_agencia
     * @Rest\Get("/api/{cod_agencia}/formularios/data", name="api_get_formularios_data", options={"expose"=true})
     * @Rest\View
     */
    public function getFormulariosDataAction($cod_agencia) {
        $em = $this->getDoctrine()->getManager();

        $agencia = $em->getRepository("IndicadoresBundle:Agencia")->findOneBy(array('codigo' => $cod_agencia));

        $response = new Response();

        $respuesta = array('state' => 'ok');
        $datos = array();
        $frmRepository = $em->getRepository('GridFormBundle:Formulario');

        foreach ($agencia->getFormularios() as $frm) {
            try {
                $datosFormulario = $frmRepository->getDatosRAW($frm);
                $datos[] = array('formulario_id' => $frm->getId(), 'formulario_codigo' => $frm->getCodigo(),
                    'nombre' => $frm->getNombre(), 'filas' => $datosFormulario);
            } catch (Exception $exc) {
                $respuesta = array('state' => 'fail', 'message' => $exc->getTraceAsString());
                $response->setContent(json_encode($respuesta));
                return $response;
            }
        }
        $respuesta['datos'] = $datos;
        $response->setContent(json_encode($respuesta));
        return $response;
    }

}
