<?php

namespace App\RESTController;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Get;
use Predis;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\SignificadoCampo;
use App\Entity\FichaTecnica;
use App\AlmacenamientoDatos\AlmacenamientoProxy;

class IndicadorRESTController extends Controller {

    protected $tamanio = 50000;

    /**
     * @param integer $fichaTec
     * @param string $dimension
     * @Get("/indicador/{id}/data/dimension/{dimension}", options={"expose"=true})
     * @Rest\View
     */
    public function getIndicadorAction(FichaTecnica $fichaTec, $dimension, Request $request, AlmacenamientoProxy $almacenamiento) {
        $response = new Response();
        $redis = new Predis\Client();
        $t = $this->get('translator');

        $filtro = $request->get('filtro');
        $verSql = ($request->get('ver_sql') == 'true') ? true : false;
        $verAnalisisDescriptivo = ($request->get('analisis_descriptivo') == 'true') ? true : false;
        $hash = md5($filtro.$verSql);

        // verifica que la respuesta no se ha modificado para la petición dada
        if ($fichaTec->getUpdatedAt() != '' and $fichaTec->getUltimaLectura() != '' and $fichaTec->getUltimaLectura() < $fichaTec->getUpdatedAt()
            and $verAnalisisDescriptivo == false ) {
            // Buscar la petición en la caché de Redis
            $respj = $redis->get('indicador_'.$fichaTec->getId().'_'.$dimension.$hash);
            if ($respj != null){
                $dat = json_decode($respj);
                if ( count($dat->datos) > 0){
                    $response->setContent($respj);
                    return $response;
                }
            }
        }
        //La respuesta de la petición no estaba en Redis, hacer el cálculo
        $resp = array();

        if ($filtro == null or $filtro == '')
            $filtros = null;
        else {

            $filtrObj = json_decode($filtro);
            foreach ($filtrObj as $f) {
                $filtros_dimensiones[] = $f->codigo;
                $filtros_valores[] = $f->valor;
            }
            $filtros = array_combine($filtros_dimensiones, $filtros_valores);
        }

        $em = $this->getDoctrine()->getManager();

        $almacenamiento->crearIndicador($fichaTec, $dimension, $filtros);
        $resp['datos'] = $almacenamiento->calcularIndicador($fichaTec, $dimension, $filtros, $verSql);

        $respj = json_encode($resp);

        if ( is_array($resp['datos']) ){
            $redis->set('indicador_'.$fichaTec->getId().'_'.$dimension.$hash, $respj);
        }

        if ($verAnalisisDescriptivo){
            $sql = $resp['datos'];
            $dimensionObj = $em->getRepository(SignificadoCampo::class)->findOneBy(array('codigo'=>$dimension));

            $datos = array_pop($almacenamiento->getAnalisisDescriptivo($sql));

            $tabla = "
                <TABLE CLASS= 'table table-striped'>
                    <THEAD>
                        <TR>
                            
                            <TH>".$t->trans('_promedio_')."</TH>
                            <TH>".$t->trans('_desviacion_estandar_')."</TH>
                            <TH>".$t->trans('_maximo_')."</TH>
                            <TH>".$t->trans('_tercer_cuartil_')."</TH>
                            <TH>".$t->trans('_segundo_cuartil_mediana_')."</TH>
                            <TH>".$t->trans('_primer_cuartil_')."</TH>
                            <TH>".$t->trans('_minimo_')."</TH>
                        </TR>
                    </THEAD>
                    <TBODY>
                        ";
            //foreach ($datos AS $d){
            $tabla .= "
                        <TR>
                            
                            <TD>$datos[promedio]</TD>
                            <TD>$datos[desviacion_estandar]</TD>
                            <TD>$datos[max]</TD>
                            <TD>$datos[cuartil_3]</TD>
                            <TD>$datos[cuartil_2]</TD>
                            <TD>$datos[cuartil_1]</TD>
                            <TD>$datos[min]</TD>
                        </TR>
                ";
            //}
            $tabla .= " 
                    </TBODY>
                </TABLE>
                ";
            $resp['datos'] = $tabla;
            $respj = json_encode($resp);
        }



        $response->setContent($respj);
        return $response;

    }

    /**
     * Obtener los datos del indicador sin aplicar la fórmula ni filtros
     * @param integer $fichaTec
     * @param string $dimension
     * @Get("/rest-service/data/{id}", options={"expose"=true})
     * @Rest\View
     */
    public function getDatosIndicadorAction(FichaTecnica $fichaTec, Request $request, AlmacenamientoProxy $almacenamiento) {
        $response = new Response();

        $redis = new Predis\Client();

        $em = $this->getDoctrine()->getManager();

        $parte = $request->get('parte');
        $porcion = ( $parte == null ) ? 0 : ($parte - 1);

        $alertas_ = $fichaTec->getAlertas();
        $alertas = [];
        foreach ( $alertas_ as $a ){
            $alertas[] = ['li'=>$a->getLimiteInferior(),
                'ls'=>$a->getLimiteSuperior(),
                'color' => $a->getColor()->getCodigo()
            ];
        }

        if ($fichaTec->getUpdatedAt() != '' and $fichaTec->getUltimaLectura() != '' and $fichaTec->getUltimaLectura() < $fichaTec->getUpdatedAt()) {
            // Buscar la petición en la caché de Redis

            $respj = $redis->get('indicador_'.$fichaTec->getId().'_parte_'.$porcion);
            if ($respj != null){
                $resp = '{"datos": '. $respj.', "total_partes": "'. $redis->get('indicador_'.$fichaTec->getId().'_tamanio_') . '"}';
                $response->setContent($resp);
                return $response;
            }
        }

        $totalRegistros = $almacenamiento->totalRegistrosIndicador($fichaTec);
        $almacenamiento->crearIndicador($fichaTec);
        $resp = $almacenamiento->getDatosIndicador($fichaTec, $porcion * $this->tamanio, $this->tamanio);
        $respX['datos'] = $resp;
        $respX['alertas'] = $alertas;

        $respX['formula'] = $fichaTec->getFormula();
        $respX['total_partes'] = ceil($totalRegistros / $this->tamanio );

        $redis->set('indicador_'.$fichaTec->getId().'_tamanio_', $respX['total_partes']);
        $redis->set('indicador_'.$fichaTec->getId().'_parte_'.$porcion, json_encode($resp));

        $response->setContent(json_encode($respX));
        return $response;

    }

    /**
     * @Get("/rest-service/indicadores", options={"expose"=true})
     * @Rest\View
     */
    public function getIndicadoresAction() {

        $response = new Response();
        $em = $this->getDoctrine()->getManager();

        $resp = array();


        //Recuperar todos los indicadores disponibles
        $indicadores = $em->getRepository(FichaTecnica::class)->findBy(array(), array('nombre' => 'ASC'));

        foreach($indicadores as $ind){
            $indicador = array();

            $indicador['id'] = $ind->getId();
            $indicador['nombre'] = $ind->getNombre();
            $indicador['unidadMedida'] = $ind->getUnidadMedida();
            $indicador['formula'] = $ind->getFormula();

            $variables = array();
            foreach($ind->getVariables() as $var){
                $variables[] = array('iniciales'=>$var->getIniciales(), 'nombre'=>$var->getNombre());
            }
            $indicador['variables'] = $variables;

            $indicador['dimensiones'] = $ind->getCamposIndicador();

            $resp[] = $indicador;
        }

        $response->setContent(json_encode($resp));

        return $response;
    }


    /**
     * @Get("/rest-service/indicador/{id}/campos", options={"expose"=true})
     * @Rest\View
     */
    public function getCamposVariablesIndicadorAction(FichaTecnica $fichaTec) {
        $em = $this->getDoctrine()->getManager();
        $campos = explode(', ', $fichaTec->getCamposIndicador() );
        $campos_ = [];
        foreach ($campos as $c){
            $significado = $em->getRepository(SignificadoCampo::class)->findOneByCodigo($c);
            $campos_[ $c ] = $significado->getDescripcion();
        }


        foreach ($fichaTec->getVariables() as $v ){
            $campos_[ '__' . strtolower( $v->getIniciales() ) . '__'] = $v->getNombre();
        }

        return new JsonResponse($campos_);
    }
}