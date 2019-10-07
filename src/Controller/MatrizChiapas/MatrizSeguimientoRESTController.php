<?php

namespace App\Controller\MatrizChiapas;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Get;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

use App\AlmacenamientoDatos\AlmacenamientoProxy;

use App\Entity\FichaTecnica;
use App\Entity\SignificadoCampo;
use App\Entity\User;
use App\Entity\Alerta;

use App\Entity\MatrizChiapas\MatrizSeguimientoMatriz;
use App\Entity\MatrizChiapas\MatrizIndicadoresDesempeno;
use App\Entity\MatrizChiapas\MatrizIndicadoresEtab;
use App\Entity\MatrizChiapas\MatrizIndicadoresEtabAlertas;
use App\Entity\MatrizChiapas\MatrizIndicadoresRel;
use App\Entity\MatrizChiapas\MatrizIndicadoresRelAlertas;
use App\Entity\MatrizChiapas\MatrizIndicadoresUsuario;

use App\Entity\MatrizChiapas\MatrizSeguimiento;
use App\Entity\MatrizChiapas\MatrizSeguimientoDato;


use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;


class MatrizSeguimientoRESTController extends Controller {

    /**
     * @return Response
     *
     * @Route("/indicadores/matrizseguimiento/matrizConfiguracion", name="matrizConfiguracion")
     */
    public function MatrizConfiguracionAction()
    {        
        return $this->render('Matriz/configurarMatriz.html.twig', array('admin_pool'    => $this->container->get('sonata.admin.pool')));
    }
    /**
     * @return Response
     *
     * @Route("/indicadores/matrizseguimiento/matrizPlaneacion", name="matrizPlaneacion")
     */
    public function MatrizPlaneacionAction()
    {        
        return $this->render('Matriz/planeacion.html.twig', array('admin_pool'    => $this->container->get('sonata.admin.pool')));
    }
    /**
     * @return Response
     *
     * @Route("/indicadores/matrizseguimiento/matrizReal", name="matrizReal")
     */
    public function MatrizRealAction()
    {        
        return $this->render('Matriz/real.html.twig', array('admin_pool'    => $this->container->get('sonata.admin.pool')));
    }
    /**
     * @return Response
     *
     * @Route("/indicadores/matrizseguimiento/matrizReporte", name="matrizReporte")
     */
    public function MatrizReporteAction()
    {        
        return $this->render('Matriz/reporte.html.twig', array('admin_pool'    => $this->container->get('sonata.admin.pool')));
    }
    public function listAction(){
        return $this->render('Matriz/reporte.html.twig', array('admin_pool'    => $this->container->get('sonata.admin.pool')));
    }


    /**
     * @Route("/api/v1/matriz/matriz", name="matriz_matriz", methods={"GET"})
     * 
     * @SWG\Get(
     *      tags={"Matriz"},
     *      summary="Lista de matrices",
     *      description="Lista las matrices diponibles para el usuario logueado",
     *      consumes={"application/json"},
     *      produces={"application/json"},
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="Regresa objecto actualizado"     
     *  ),
     * 
     * @SWG\Response(
     *     response=400,
     *     description="No se envio los parametros necesarios"     
     *  ),
     * 
     * @SWG\Response(
     *     response=404,
     *     description="El elemento no existe"     
     *  ),
     * 
     *  @SWG\Response(
     *     response=500,
     *     description="Regresa un error ocurrido en el servidor"     
     *  ),
     */
    public function matrices(){
        $em = $this->getDoctrine()->getEntityManager();
        $where = '';
        if ( !$this->getUser()->hasRole('ROLE_SUPER_ADMIN') ) {
            $connection = $em->getConnection();
            $statement = $connection->prepare("SELECT * FROM matriz_indicadores_usuario WHERE id_usuario = ".$this->getUser()->getId());
            $statement->execute();
            $permitido = $statement->fetchAll();

            $in = [];
            foreach($permitido as $p){
                array_push($in, $p["id_matriz"]);
            }
            if(count($in) > 0){
                $in = implode(",", $in);
                $where = "WHERE id in($in)";
            }
        }

        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT * FROM matriz_seguimiento_matriz $where ORDER BY nombre ASC");
        $statement->execute();
        $matriz = $statement->fetchAll();

        if($matriz){
            $resp = ["data" => $matriz, "mensaje" => $this->get('translator')->trans('_cargado_correctamente'), "status" => 200];
        }else{
            $resp = ["data" => "false", "mensaje" => $this->get('translator')->trans('_ningun_dato_'), "status" => 404];
        }  
        $response = new Response();
        $response->setContent(json_encode($resp));

        return $response;
    }
    /**
     * @Route("/api/v1/matriz/planeacion", name="matriz_planeacion", methods={"GET"})
     * 
     * @SWG\Get(
     *      tags={"Matriz"},
     *      summary="Planeacion",
     *      description="Devuelve los datos de la planeacion de la matriz y el año seleccionado",
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      @SWG\Parameter(parameter="a_in_path", name="anio", type="integer", in="query"),
     *      @SWG\Parameter(parameter="m_in_path", name="matrix", type="integer", in="query")
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="Regresa objecto"     
     *  ),
     * 
     * @SWG\Response(
     *     response=400,
     *     description="No se envio los parametros necesarios"     
     *  ),
     * 
     * @SWG\Response(
     *     response=404,
     *     description="El elemento no existe"     
     *  ),
     * 
     *  @SWG\Response(
     *     response=500,
     *     description="Regresa un error ocurrido en el servidor"     
     *  ),
     */
    public function planeacion(Request $request){
        $response = new Response();

        $anio   = $request->query->get('anio');
        $matrix = $request->query->get('matrix');

        $em = $this->getDoctrine()->getEntityManager();

        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT distinct(ms.id_desempeno), mid.orden FROM matriz_seguimiento ms 
            LEFT JOIN matriz_indicadores_desempeno mid ON mid.id = ms.id_desempeno WHERE anio = '$anio' and id_matriz = '$matrix'  ORDER BY mid.orden ASC");
        $statement->execute();
        $matriz = $statement->fetchAll();

        if($matriz){
            $resp = array(); $indicadores_in = array();
            foreach ($matriz as $key => $value) {
                $value = (object) $value;
                $ind = $em->getRepository(MatrizIndicadoresDesempeno::class)->find($value->id_desempeno);
                if($ind){
                    
                    $indicador = array();
                    $indicadores_in[] = $ind->getId();
                    $indicador['id'] = $ind->getId();
                    $indicador['nombre'] = $ind->getNombre();
                    
                    $indicators = array(); $i=0;
                    $relaciones = $em->getRepository(MatrizIndicadoresRel::class)->findBy(array('desempeno' => $ind->getId()), array('id' => 'ASC'));
                
                    foreach($relaciones as $indrs){
                        
                        $connection = $em->getConnection();
                        $statement = $connection->prepare("SELECT msd.mes, msd.planificado, ms.meta FROM matriz_seguimiento ms 
                            LEFT JOIN matriz_seguimiento_dato msd ON msd.id_matriz = ms.id   
                            WHERE ms.anio = '$anio' and ms.etab = false and ms.id_desempeno = '".$value->id_desempeno."' and indicador = '".$indrs->getId()."'");
                        $statement->execute();
                        $meses = $statement->fetchAll();
                        $meta = 0;
                        if(isset($meses[0]))
                            $meta = $meses[0]["meta"];
                        $indicators[$i] = array('id'=>$indrs->getId(), 'nombre'=>$indrs->getNombre(), 'fuente' => ' '.$indrs->getFuente(), 'meta' => $meta);

                        foreach ($meses as $km => $vm) {
                            $vm = (object) $vm;
                            $indicators[$i][$vm->mes] = $vm->planificado;
                        }
                        $i++;
                    }

                    $etab = array(); $i=0;
                   
                    $statement = $connection->prepare("SELECT e.id, f.nombre FROM matriz_indicadores_etab AS e LEFT JOIN ficha_tecnica AS f on f.id = e.id_ficha_tecnica 
                    WHERE  e.id_desempeno = '".$ind->getId()."'");
                    $statement->execute();
                    $etabes = $statement->fetchAll();
                    foreach($etabes as $indrs){                                                               

                        $connection = $em->getConnection();
                        $statement = $connection->prepare("SELECT msd.mes, msd.planificado, ms.meta FROM matriz_seguimiento ms 
                            LEFT JOIN matriz_seguimiento_dato msd ON msd.id_matriz = ms.id   
                            WHERE ms.anio = '$anio' and ms.etab = true and ms.id_desempeno = '".$value->id_desempeno."' and indicador = '".$indrs["id"]."'");
                        $statement->execute();
                        $meses = $statement->fetchAll();
                        $meta = 0;
                        if(isset($meses[0]))
                            $meta = $meses[0]["meta"];
                        $etab[$i] = array('id'=>$indrs["id"], 'nombre'=>$indrs["nombre"], 'fuente' => ' eTAB', 'meta' => $meta);

                        foreach ($meses as $km => $vm) {
                            $vm = (object) $vm;
                            $etab[$i][$vm->mes] = $vm->planificado;
                        }
                        $i++;
                    }

                    $indicador['indicadores_etab'] = $etab;
                    $indicador['indicadores_relacion'] = $indicators;

                    $resp[] = $indicador;                    
                }
            }
           
            $indicadores_in = implode(",", $indicadores_in);
            $statement = $connection->prepare("SELECT id FROM matriz_indicadores_desempeno WHERE id not in($indicadores_in) and id_matriz = '".$matrix."'");
            $statement->execute();
            $indicadores = $statement->fetchAll();            

            if($indicadores){
                foreach($indicadores as $indicator){
                    $indicador = array();
                    
                    $ind = $em->getRepository(MatrizIndicadoresDesempeno::class)->find($indicator["id"]);

                    $indicador['id'] = $ind->getId();
                    $indicador['nombre'] = $ind->getNombre();
                    
                    $indicators = array();                    
                    $relaciones = $em->getRepository(MatrizIndicadoresRel::class)->findBy(array('desempeno' => $ind->getId()), array('id' => 'ASC'));
                    foreach($relaciones as $indrs){
                        $indicators[] = array('id'=>$indrs->getId(), 'nombre'=>$indrs->getNombre());
                    }

                    $etab = array();
                    $statement = $connection->prepare("SELECT e.id, f.nombre FROM matriz_indicadores_etab AS e LEFT JOIN ficha_tecnica AS f on f.id = e.id_ficha_tecnica 
                    WHERE  e.id_desempeno = '".$ind->getId()."'");
                    $statement->execute();
                    $etabes = $statement->fetchAll();
                    foreach($etabes as $indrs){                    
                        $etab[] = array('id'=>$indrs["id"], 'nombre'=>$indrs["nombre"]);
                    }

                    $indicador['indicadores_etab'] = $etab;
                    $indicador['indicadores_relacion'] = $indicators;

                    $resp[] = $indicador;
                }
            }
            $resp = ["data" => $resp, "mensaje" => $this->get('translator')->trans('_cargado_correctamente'), "status" => 200];
        }else{
            $resp = ["data" => "false", "mensaje" => $this->get('translator')->trans('_ningun_dato_'), "status" => 404];
        }
        
        $response->setContent(json_encode($resp));

        return $response;
    }

    /**
     * @Route("/api/v1/matriz/planeacion/crear", name="matriz_planeacion_crear", methods={"GET"})
     * 
     * @SWG\Get(
     *      tags={"Matriz"},
     *      summary="Planeacion crear",
     *      description="Crea el formulario para crear la planeacion de una matriz en el año seleccionado",
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      @SWG\Parameter(parameter="a_in_path", name="anio", type="integer", in="query"),
     *      @SWG\Parameter(parameter="m_in_path", name="matrix", type="integer", in="query")
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="Regresa objecto"     
     *  ),
     * 
     * @SWG\Response(
     *     response=400,
     *     description="No se envio los parametros necesarios"     
     *  ),
     * 
     * @SWG\Response(
     *     response=404,
     *     description="El elemento no existe"     
     *  ),
     * 
     *  @SWG\Response(
     *     response=500,
     *     description="Regresa un error ocurrido en el servidor"     
     *  ),
     */
    public function planeacionCrear(Request $request){
        $response = new Response();
        $resp = array();
        $anio   = $request->query->get('anio');
        $matrix = $request->query->get('matrix');

        $em = $this->getDoctrine()->getManager();     
        $connection = $em->getConnection();

        $indicadores = $em->getRepository(MatrizIndicadoresDesempeno::class)->findBy(array('matriz' => $matrix), array('id' => 'ASC'));
        if($indicadores){
            foreach($indicadores as $ind){
                $indicador = array();
            
                $indicador['id'] = $ind->getId();
                $indicador['nombre'] = $ind->getNombre();
                
                $indicators = array();
                $relaciones = $em->getRepository(MatrizIndicadoresRel::class)->findBy(array('desempeno' => $ind->getId()), array('id' => 'ASC'));
                foreach($relaciones as $indrs){
                    $indicators[] = array('id'=>$indrs->getId(), 'nombre'=>$indrs->getNombre());
                }

                $etab = array();
                $statement = $connection->prepare("SELECT e.id, f.nombre FROM matriz_indicadores_etab AS e LEFT JOIN ficha_tecnica AS f on f.id = e.id_ficha_tecnica 
                WHERE  e.id_desempeno = '".$ind->getId()."'");
                $statement->execute();
                $etabes = $statement->fetchAll();
                foreach($etabes as $indrs){                    
                    $etab[] = array('id'=>$indrs["id"], 'nombre'=>$indrs["nombre"]);
                }

                $indicador['indicadores_etab'] = $etab;
                $indicador['indicadores_relacion'] = $indicators;

                $resp[] = $indicador;
            }
            $resp = ["data" => $resp  , "mensaje" => $this->get('translator')->trans('_cargado_correctamente'), "status" => 200];
        }else{
            $resp = ["data" => "false", "mensaje" => $this->get('translator')->trans('_ningun_dato_'), "status" => 404];
        }
        
        $response->setContent(json_encode($resp));

        return $response;
    }

    /**
     * @Route("/api/v1/matriz/planeacion/guardar", name="matriz_planeacion_guardar", methods={"POST"})
     * 
     * @SWG\Post(
     *      tags={"Matriz"},
     *      summary="Guardar planeacion",
     *      description="Guarda los valores de la planeacion de una matriz en el año seleccionado",
     *      produces={"application/json"},  
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JSON con los filtros",
     *          type="object",
     *          format="application/json",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="anio", type="string", example="2017"),
     *              @SWG\Property(property="matrix", type="string", example="19"),
     *              @SWG\Property(property="matriz", type="array", 
     *                  @SWG\Items(
     *                      @SWG\Property(property="id", type="string", example="19"),
     *                      @SWG\Property(property="nombre", type="string", example="uno"),
     *                      @SWG\Property(property="indicadores_relacion", type="array",
     *                          @SWG\Items(
     *                              @SWG\Property(property="id", type="string", example="17"),
     *                              @SWG\Property(property="nombre", type="string", example="ind 1"),
     *                              @SWG\Property(property="fuente", type="string", example="fuente 1"),
     *                              @SWG\Property(property="meta", type="string", example="1700"),
     *                              @SWG\Property(property="enero", type="string", example="100"),
     *                              @SWG\Property(property="febrero", type="string", example="123"),
     *                              @SWG\Property(property="marzo", type="string", example="170"),
     *                              @SWG\Property(property="abril", type="string", example="175"),
     *                              @SWG\Property(property="mayo", type="string", example="179"),
     *                              @SWG\Property(property="junio", type="string", example="179"),
     *                              @SWG\Property(property="julio", type="string", example="179"),
     *                              @SWG\Property(property="agosto", type="string", example="189"),
     *                              @SWG\Property(property="septiembre", type="string", example="187"),
     *                              @SWG\Property(property="octubre", type="string", example="180"),
     *                              @SWG\Property(property="noviembre", type="string", example="177"),
     *                              @SWG\Property(property="diciembre", type="string", example="165")
     *                          )
     *                      ),
     *                      @SWG\Property(property="indicadores_etab", type="array",
     *                          @SWG\Items(
     *                              @SWG\Property(property="id", type="string", example="50"),
     *                              @SWG\Property(property="nombre", type="string", example="ind 1"),
     *                              @SWG\Property(property="fuente", type="string", example="fuente 1"),
     *                              @SWG\Property(property="meta", type="string", example="1700"),
     *                              @SWG\Property(property="enero", type="string", example="100"),
     *                              @SWG\Property(property="febrero", type="string", example="123"),
     *                              @SWG\Property(property="marzo", type="string", example="170"),
     *                              @SWG\Property(property="abril", type="string", example="175"),
     *                              @SWG\Property(property="mayo", type="string", example="179"),
     *                              @SWG\Property(property="junio", type="string", example="179"),
     *                              @SWG\Property(property="julio", type="string", example="179"),
     *                              @SWG\Property(property="agosto", type="string", example="189"),
     *                              @SWG\Property(property="septiembre", type="string", example="187"),
     *                              @SWG\Property(property="octubre", type="string", example="180"),
     *                              @SWG\Property(property="noviembre", type="string", example="177"),
     *                              @SWG\Property(property="diciembre", type="string", example="165")
     *                          )
     *                      )
     *                  )
     *              )
     *          )
     *      )
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="Regresa objecto"     
     *  ),
     * 
     * @SWG\Response(
     *     response=404,
     *     description="El elemento no existe"     
     *  ),
     * 
     *  @SWG\Response(
     *     response=500,
     *     description="Regresa un error ocurrido en el servidor"     
     *  ),
     */
    public function planeacionGuardar(Request $request){
        $response = new Response();
        $resp = array();
        $em = $this->getDoctrine()->getEntityManager(); 
        $bien = true;
        $anio   = $request->request->get('anio');
        $matriz = $request->request->get('matriz');
        $existe = $em->getRepository(MatrizSeguimiento::class)->findBy(
            array(
                'anio' => $anio
            )
        );

        foreach ($matriz as $key => $value) {
            $value = (object) $value;

            if(isset($value->indicadores_etab))
            foreach ($value->indicadores_etab as $ke => $ve) {                                                            
                if(!$this->insertSeguimientoDato($em, $existe, $value, $ke, $ve, $anio, TRUE))
                    $bien = false;                
            }
            if(isset($value->indicadores_relacion))
            foreach ($value->indicadores_relacion as $ke => $ve) {                                                            
                if(!$this->insertSeguimientoDato($em, $existe, $value, $ke, $ve, $anio, FALSE))
                    $bien = false;                
            }
        }
        if($bien){
            $resp = ["data" => "true" , "mensaje" => $this->get('translator')->trans('_guardar_ok_'), "status" => 200];
        }else{
            $resp = ["data" => "false", "mensaje" => $this->get('translator')->trans('_guardar_no_ok_'), "status" => 404];
        }
        
        $response->setContent(json_encode($resp));

        return $response;
    }

    public function insertSeguimientoDato($em, $existe, $value, $ke, $ve, $anio, $etab){
        $ve = (object) $ve; 
        try{

            $seguimiento = $em->getRepository(MatrizSeguimiento::class)->findBy(
                array(
                    'desempeno' => $value->id,
                    'anio' => $anio,
                    'indicador' => $ve->id,
                    'etab' => $etab
                )
            );
            if($seguimiento)
                $seguimiento = $em->getRepository(MatrizSeguimiento::class)->find($seguimiento[0]->getId());
            else{
                $seguimiento = new MatrizSeguimiento();
            }

            $seguimiento->setAnio($anio);
            $seguimiento->setEtab($etab);
            $seguimiento->setIndicador($ve->id);
            if(isset($ve->meta))
                $seguimiento->setMeta($ve->meta);

            $desempeno = $em->getRepository(MatrizIndicadoresDesempeno::class)->find($value->id);

            $seguimiento->setDesempeno($desempeno);
            if(isset($value->meta))
                $seguimiento->setMeta($value->meta); 

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($seguimiento);
            $em->flush();

            foreach ($ve as $k1 => $v1) {
                if($k1 != "meta" && $k1 != "fuente"  && $k1 != "id" && $k1 != "nombre" && $k1 != '$$hashKey'){
                    $matrizDato = $em->getRepository(MatrizSeguimientoDato::class)->findBy(
                        array(
                            'matriz' => $seguimiento->getId(),
                            'mes' => $k1
                        )
                    );

                    if($matrizDato){
                        $matrizDato = $em->getRepository(MatrizSeguimientoDato::class)->find($matrizDato[0]->getId());
                    }
                    else{
                        $matrizDato = new MatrizSeguimientoDato();                    
                    }

                    $matrizDato->setMes($k1);
                    $matrizDato->setPlanificado($v1);
                    $matrizDato->setMatriz($seguimiento);

                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($matrizDato);
                    $em->flush();
                }
            }
            
            return true;
        }catch(\Exception $e){
            var_dump($e->getMessage());
            return false;
        }
    }



    //REAL

    /**
     * @Route("/api/v1/matriz/real", name="matriz_real", methods={"GET"})
     * 
     * @SWG\Get(
     *      tags={"Matriz"},
     *      summary="Real",
     *      description="Devuelve los datos de la captura real de la matriz y el año seleccionado",
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      @SWG\Parameter(parameter="a_in_path", name="anio", type="integer", in="query"),
     *      @SWG\Parameter(parameter="m_in_path", name="matrix", type="integer", in="query")
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="Regresa objecto"     
     *  ),
     * 
     * @SWG\Response(
     *     response=400,
     *     description="No se envio los parametros necesarios"     
     *  ),
     * 
     * @SWG\Response(
     *     response=404,
     *     description="El elemento no existe"     
     *  ),
     * 
     *  @SWG\Response(
     *     response=500,
     *     description="Regresa un error ocurrido en el servidor"     
     *  ),
     */
    public function real(Request $request, AlmacenamientoProxy $almacenamiento){
        $response = new Response();

        $anio   = $request->query->get('anio');
        $matrix = $request->query->get('matrix');

        $em = $this->getDoctrine()->getEntityManager();

        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT distinct(ms.id_desempeno), mid.orden FROM matriz_seguimiento ms 
            LEFT JOIN matriz_indicadores_desempeno mid ON mid.id = ms.id_desempeno WHERE anio = '$anio'  and id_matriz = '$matrix' ORDER BY mid.orden ASC");
        $statement->execute();
        $matriz = $statement->fetchAll();

        if($matriz){
            $resp = array();
            foreach ($matriz as $key => $value) {
                $value = (object) $value;
                $ind = $em->getRepository(MatrizIndicadoresDesempeno::class)->find($value->id_desempeno);
                if($ind){
                    
                    $indicador = array();
                
                    $indicador['id'] = $ind->getId();
                    $indicador['nombre'] = $ind->getNombre();
                    
                    $indicators = array(); $i=0;
                    $relaciones = $em->getRepository(MatrizIndicadoresRel::class)->findBy(array('desempeno' => $ind->getId()), array('id' => 'ASC'));
                    foreach($relaciones as $indrs){                
                        $indicators[$i] = array('id'=>$indrs->getId(), 'nombre'=>$indrs->getNombre(), 'es_formula' => $indrs->getEsFormula(), 'fuente' => ' '.$indrs->getFuente());

                        $connection = $em->getConnection();
                        $statement = $connection->prepare("SELECT msd.mes, msd.planificado, msd.real, msd.real_denominador FROM matriz_seguimiento ms 
                            LEFT JOIN matriz_seguimiento_dato msd ON msd.id_matriz = ms.id   
                            WHERE ms.anio = '$anio' and ms.etab = false and ms.id_desempeno = '".$value->id_desempeno."' and indicador = '".$indrs->getId()."'");
                        $statement->execute();
                        $meses = $statement->fetchAll();

                        foreach ($meses as $km => $vm) {
                            $vm = (object) $vm;
                            if($vm->mes != 'fuente'){
                                $indicators[$i][$vm->mes]["planificado"] = $vm->planificado;
                                $indicators[$i][$vm->mes]["real"] = $vm->real;
                                $indicators[$i][$vm->mes]["real_denominador"] = $vm->real_denominador;
                            }
                        }
                        $i++;
                    }
                    // obtener los datos del etab
                    $etab = array(); $i=0;
                    
                    $fichaRepository = $em->getRepository(FichaTecnica::class);
                    $errores = ""; $ci = 0;

                    $statement = $connection->prepare("SELECT e.*, f.nombre FROM matriz_indicadores_etab AS e LEFT JOIN ficha_tecnica AS f on f.id = e.id_ficha_tecnica 
                    WHERE  e.id_desempeno = '".$ind->getId()."'");
                    $statement->execute();
                    $etabes = $statement->fetchAll();
                    foreach($etabes as $indrs){   
                        $fichaTec = $fichaRepository->find($indrs["id_ficha_tecnica"]);
                        $filtros = json_decode($indrs["filtros"]); 
                        if($filtros){
                            $otros_filtros = (array) $filtros->otros_filtros;
                            if(!array_key_exists("elementos", $otros_filtros)){
                                $otros_filtros["elementos"] = [];
                            }
                            $keyanio = ""; 
                            foreach($filtros->dimensiones as $dim){
                                $vdim = strtoupper(trim($dim));
                                if($vdim == 'ANIO' || $vdim == 'ANIOS' || $vdim == 'YEAR' || $vdim == 'YEARS' || $vdim == 'ID_ANIO' || $vdim == 'ANIO_ID' ){
                                    $keyanio = trim($dim);
                                }                            
                            }
                            // agregar la dimension para el filtro en otros  
                            if($otros_filtros["dimension_mostrar"] != "")                                                                          
                                $dimension = trim($filtros->dimensiones[$otros_filtros["dimension_mostrar"]]);
                            else
                                $dimension = "MES";
                            $otros_filtros["dimension"] = trim($filtros->dimensiones[$filtros->dimension]);
                        }
                        $filtrar = [$keyanio => $anio]; 
                        $almacenamiento->crearIndicador($fichaTec, $otros_filtros["dimension"], $filtrar);                        

                        $ci++;
                        $etab[$i] = array('id'=>$indrs["id_ficha_tecnica"], 'nombre'=>$indrs["nombre"], 'fuente' => ' eTAB');

                        $connection = $em->getConnection();
                        $statement = $connection->prepare("SELECT msd.mes, msd.planificado, msd.real FROM matriz_seguimiento ms 
                            LEFT JOIN matriz_seguimiento_dato msd ON msd.id_matriz = ms.id   
                            WHERE ms.anio = '$anio' and ms.etab = true and ms.id_desempeno = '".$value->id_desempeno."' and indicador = '".$indrs["id_ficha_tecnica"]."'");
                        $statement->execute();
                        $meses = $statement->fetchAll();
                        $ttm = 0; $representa = 0; $id_siguiente = 0;
                        foreach ($meses as $km => $vm) {
                            $vm = (object) $vm;
                            if($vm->mes != 'fuente'){
                                $etab[$i][$vm->mes]["planificado"] = $vm->planificado;                                
                                try{
                                    $ttm++; 
                                    // obtener datos de los indicadores de etab
                                    $representa++;                                
                                    if($representa == $otros_filtros["representa"]){   
                                        $data_indicador = $this->obtenerDatosetab($ttm, $vm, $dimension, $keyanio, $anio, $fichaRepository, $fichaTec, $otros_filtros, $id_siguiente);
                                        if(count($data_indicador) == 0){
                                            $errores.= "<br>No se cargo linea: $ci mes: ".$vm->mes;
                                        }
                                        else{
                                            $measure = '';
                                            if(isset($data_indicador[0]))
                                                $measure = $data_indicador[0]["measure"];
                                            $etab[$i][$vm->mes]["real"] = $measure;
                                        }
                                        $id_siguiente++;
                                        $representa = 0;
                                    }                                                                        
                                }
                                catch(\Exception $e){
                                }                            
                            }
                        }
                        $i++;
                    }

                    $indicador['indicadores_etab'] = $etab;
                    $indicador['indicadores_relacion'] = $indicators;

                    $resp[] = $indicador;                    
                }
            }
            $resp = ["data" => $resp, "mensaje" => $this->get('translator')->trans('_cargado_correctamente').$errores, "status" => 200];
        }else{
            $resp = ["data" => "false", "mensaje" => $this->get('translator')->trans('_ningun_dato_'), "status" => 404];
        }
        
        $response->setContent(json_encode($resp));

        return $response;
    }

    /**
     * @Route("/api/v1/matriz/real/guardar", name="matriz_real_guardar", methods={"POST"})
     * 
     * @SWG\Post(
     *      tags={"Matriz"},
     *      summary="Guardar real",
     *      description="Guarda los valores de la captura real de una matriz en el año seleccionado",
     *      produces={"application/json"},  
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JSON con los filtros",
     *          type="object",
     *          format="application/json",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="anio", type="string", example="2017"),
     *              @SWG\Property(property="matrix", type="string", example="19"),
     *              @SWG\Property(property="matriz", type="array", 
     *                  @SWG\Items(
     *                      @SWG\Property(property="id", type="string", example="19"),
     *                      @SWG\Property(property="nombre", type="string", example="uno"),
     *                      @SWG\Property(property="indicadores_relacion", type="array",
     *                          @SWG\Items(
     *                              @SWG\Property(property="id", type="string", example="17"),
     *                              @SWG\Property(property="nombre", type="string", example="ind 1"),
     *                              @SWG\Property(property="meta", type="string", example="1700"),
     *                              @SWG\Property(property="enero", type="string", example="100"),
     *                              @SWG\Property(property="febrero", type="string", example="123"),
     *                              @SWG\Property(property="marzo", type="string", example="170"),
     *                              @SWG\Property(property="abril", type="string", example="175"),
     *                              @SWG\Property(property="mayo", type="string", example="179"),
     *                              @SWG\Property(property="junio", type="string", example="179"),
     *                              @SWG\Property(property="julio", type="string", example="179"),
     *                              @SWG\Property(property="agosto", type="string", example="189"),
     *                              @SWG\Property(property="septiembre", type="string", example="187"),
     *                              @SWG\Property(property="octubre", type="string", example="180"),
     *                              @SWG\Property(property="noviembre", type="string", example="177"),
     *                              @SWG\Property(property="diciembre", type="string", example="165")
     *                          )
     *                      ),
     *                      @SWG\Property(property="indicadores_etab", type="array",
     *                          @SWG\Items(
     *                              @SWG\Property(property="id", type="string", example="50"),
     *                              @SWG\Property(property="nombre", type="string", example="ind 1"),
     *                              @SWG\Property(property="fuente", type="string", example="fuente 1"),
     *                              @SWG\Property(property="meta", type="string", example="1700"),
     *                              @SWG\Property(property="enero", type="string", example="100"),
     *                              @SWG\Property(property="febrero", type="string", example="123"),
     *                              @SWG\Property(property="marzo", type="string", example="170"),
     *                              @SWG\Property(property="abril", type="string", example="175"),
     *                              @SWG\Property(property="mayo", type="string", example="179"),
     *                              @SWG\Property(property="junio", type="string", example="179"),
     *                              @SWG\Property(property="julio", type="string", example="179"),
     *                              @SWG\Property(property="agosto", type="string", example="189"),
     *                              @SWG\Property(property="septiembre", type="string", example="187"),
     *                              @SWG\Property(property="octubre", type="string", example="180"),
     *                              @SWG\Property(property="noviembre", type="string", example="177"),
     *                              @SWG\Property(property="diciembre", type="string", example="165")
     *                          )
     *                      )
     *                  )
     *              )
     *          )
     *      )
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="Regresa objecto"     
     *  ),
     * 
     * @SWG\Response(
     *     response=404,
     *     description="El elemento no existe"     
     *  ),
     * 
     *  @SWG\Response(
     *     response=500,
     *     description="Regresa un error ocurrido en el servidor"     
     *  ),
     */
    public function realGuardar(Request $request){
        $response = new Response();
        $resp = array();
        $em = $this->getDoctrine()->getEntityManager(); 
        $bien = true;
        $anio   = $request->request->get('anio');
        $matriz = $request->request->get('matriz');
        $existe = $em->getRepository(MatrizSeguimiento::class)->findBy(
            array(
                'anio' => $anio
            )
        );

        foreach ($matriz as $key => $value) {
            $value = (object) $value; 

            if(isset($value->indicadores_etab))
            foreach ($value->indicadores_etab as $ke => $ve) {                                                            
                if(!$this->insertSeguimientoRealDato($em, $existe, $value, $ke, $ve, $anio, TRUE))
                    $bien = false;                
            }
            
            if(isset($value->indicadores_relacion))
            foreach ($value->indicadores_relacion as $ke => $ve) {  
                                                                     
                if(!$this->insertSeguimientoRealDato($em, $existe, $value, $ke, $ve, $anio, FALSE))
                    $bien = false;                
            }
        }
        if($bien){
            $resp = ["data" => "true" , "mensaje" => $this->get('translator')->trans('_guardar_ok_'), "status" => 200];
        }else{
            $resp = ["data" => "false", "mensaje" => $this->get('translator')->trans('_guardar_no_ok_'), "status" => 404];
        }
        
        $response->setContent(json_encode($resp));

        return $response;
    }

    public function insertSeguimientoRealDato($em, $existe, $value, $ke, $ve, $anio, $etab){

        $ve = (object) $ve; 
        try{
            if($existe){         

                $seguimiento = $em->getRepository(MatrizSeguimiento::class)->findBy(
                    array(
                        'desempeno' => $value->id,
                        'anio' => $anio,
                        'indicador' => $ve->id,
                        'etab' => $etab
                    )
                );
                
                if ($seguimiento)
                    $seguimiento = $em->getRepository(MatrizSeguimiento::class)->find($seguimiento[0]->getId());
                else {
                    $seguimiento = new MatrizSeguimiento();
                    $seguimiento->setAnio($anio);
                    $seguimiento->setEtab($etab);
                    $seguimiento->setIndicador($ve->id);                    

                    $desempeno = $em->getRepository(MatrizIndicadoresDesempeno::class)->find($value->id);

                    $seguimiento->setDesempeno($desempeno);                    

                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($seguimiento);
                    $em->flush();

                    $seguimiento = $em->getRepository(MatrizSeguimiento::class)->find($seguimiento->getId());
                }

                                
                foreach ($ve as $k1 => $v1) {

                    if($k1 != "meta" && $k1 != "id" && $k1 != "nombre" && $k1 != '$$hashKey'){
                        if(isset($v1["real"])){
                            if($v1["real"] == "")
                                $v1["real"] = null;
                            
                            if($v1["real_denominador"] == "")
                                $v1["real_denominador"] = null;
                            
                            $matrizDato = $em->getRepository(MatrizSeguimientoDato::class)->findBy(
                                array(
                                    'matriz' => $seguimiento->getId(),
                                    'mes' => $k1
                                )
                            );

                            if($matrizDato){
                                $matrizDato = $em->getRepository(MatrizSeguimientoDato::class)->find($matrizDato[0]->getId());
                            }
                            else{
                                $matrizDato = new MatrizSeguimientoDato();                    
                            }  

                            $matrizDato->setReal($v1["real"]);
                            $matrizDato->setRealDenominador($v1["real_denominador"]);
                            $matrizDato->setMes($k1);
                            $matrizDato->setMatriz($seguimiento);                      

                            $em = $this->getDoctrine()->getEntityManager();
                            $em->persist($matrizDato);
                            $em->flush();
                        }
                    }
                }
            }
            return true;
        }catch(\Exception $e){
            var_dump($e->getMessage());
            return false;
        }
    }

    //REPORTE

    /**
     * @Route("/api/v1/matriz/reporte", name="matriz_reporte", methods={"GET"})
     * 
     * @SWG\Get(
     *      tags={"Matriz"},
     *      summary="Reporte",
     *      description="Devuelve los datos de la captura real y de planeacion para generar el reporte de la matriz y el año seleccionado",
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      @SWG\Parameter(parameter="a_in_path", name="anio", type="integer", in="query"),
     *      @SWG\Parameter(parameter="m_in_path", name="matrix", type="integer", in="query")
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="Regresa objecto"     
     *  ),
     * 
     * @SWG\Response(
     *     response=400,
     *     description="No se envio los parametros necesarios"     
     *  ),
     * 
     * @SWG\Response(
     *     response=404,
     *     description="El elemento no existe"     
     *  ),
     * 
     *  @SWG\Response(
     *     response=500,
     *     description="Regresa un error ocurrido en el servidor"     
     *  ),
     */
    public function reporte(Request $request, AlmacenamientoProxy $almacenamiento){
        $response = new Response();

        $anio   = $request->query->get('anio');
        $matrix = $request->query->get('matrix');

        $em = $this->getDoctrine()->getEntityManager();

        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT distinct(ms.id_desempeno), mid.orden 
            FROM matriz_seguimiento ms 
            LEFT JOIN matriz_indicadores_desempeno mid ON mid.id = ms.id_desempeno 
            WHERE ms.anio = '$anio'  and mid.id_matriz = '$matrix' ORDER BY mid.orden ASC");
        $statement->execute();
        $matriz = $statement->fetchAll();

        if($matriz){
            $resp = array();
            foreach ($matriz as $key => $value) {
                $value = (object) $value;
                $ind = $em->getRepository(MatrizIndicadoresDesempeno::class)->find($value->id_desempeno);
                if($ind){
                    
                    $indicador = array();
                
                    $indicador['id'] = $ind->getId();
                    $indicador['nombre'] = $ind->getNombre();
                    
                    $indicators = array(); $i=0;
                    $relaciones = $em->getRepository(MatrizIndicadoresRel::class)->findBy(array('desempeno' => $ind->getId()), array('id' => 'ASC'));
                    foreach($relaciones as $indrs){   
                                            
                        $statement = $connection->prepare("SELECT msd.mes, msd.planificado, msd.real, msd.real_denominador, ms.meta 
                            FROM matriz_seguimiento ms 
                            LEFT JOIN matriz_seguimiento_dato msd ON msd.id_matriz = ms.id   
                            WHERE ms.anio = '$anio' and ms.etab = false and ms.id_desempeno = '".$value->id_desempeno."' and indicador = '".$indrs->getId()."'");
                        $statement->execute();
                        $meses = $statement->fetchAll();
                        $meta = '';
                        if(isset($meses[0]))
                            $meta = $meses[0]["meta"];
                        $indicators[$i] = array('id'=>$indrs->getId(), 'nombre'=>$indrs->getNombre(), 'es_formula' => $indrs->getEsFormula(), 'fuente' => ' '.$indrs->getFuente(), 'meta' => $meta);

                        $statement = $connection->prepare("SELECT * FROM matriz_indicadores_relacion_alertas WHERE matriz_indicador_relacion_id = '".$indrs->getId()."'");
                        $statement->execute();
                        $alertas = $statement->fetchAll();
                        foreach ($alertas as $a1 => $alerta) {
                            $alertas[$a1]["color"] = json_decode($alerta["color"]);
                        }
                        $indicators[$i]["alertas"] = $alertas;      

                        foreach ($meses as $km => $vm) {
                            $vm = (object) $vm; 
                            
                            if($vm->mes != 'fuente'){                                
                                $indicators[$i][$vm->mes]["planificado"] = $vm->planificado;                            
                                $indicators[$i][$vm->mes]["real"] = $vm->real;
                                $indicators[$i][$vm->mes]["real_denominador"] = $vm->real_denominador;                                                     
                            }
                        }
                        $i++;
                    }

                    $etab = array(); $i=0;
                    $fichaRepository = $em->getRepository(FichaTecnica::class);

                    $statement = $connection->prepare("SELECT e.*, f.nombre FROM matriz_indicadores_etab AS e LEFT JOIN ficha_tecnica AS f on f.id = e.id_ficha_tecnica 
                    WHERE  e.id_desempeno = '".$ind->getId()."'");
                    $statement->execute();
                    $etabes = $statement->fetchAll();
                    foreach($etabes as $ci => $indrs){  
                        
                        $statement = $connection->prepare("SELECT * FROM matriz_indicadores_etab_alertas WHERE matriz_indicador_etab_id = '".$indrs["id"]."'");
                        $statement->execute();
                        $alertas = $statement->fetchAll();     
                        foreach ($alertas as $a1 => $alerta) {
                            $alertas[$a1]["color"] = json_decode($alerta["color"]);
                        }                 

                        $fichaTec = $fichaRepository->find($indrs["id_ficha_tecnica"]);
                        $filtros = json_decode($indrs["filtros"]); 
                        if($filtros){
                            $otros_filtros = (array) $filtros->otros_filtros;
                            if(!array_key_exists("elementos", $otros_filtros)){
                                $otros_filtros["elementos"] = [];
                            }
                            $keyanio = ""; 
                            foreach($filtros->dimensiones as $dim){
                                $vdim = strtoupper(trim($dim));
                                if($vdim == 'ANIO' || $vdim == 'ANIOS' || $vdim == 'YEAR' || $vdim == 'YEARS' || $vdim == 'ID_ANIO' || $vdim == 'ANIO_ID' ){
                                    $keyanio = trim($dim);
                                }                            
                            }
                            // agregar la dimension para el filtro en otros 
                            if($otros_filtros["dimension_mostrar"] != '')                                                                           
                                $dimension = trim($filtros->dimensiones[$otros_filtros["dimension_mostrar"]]);
                            else
                                $dimension = "MES";
                            $otros_filtros["dimension"] = trim($filtros->dimensiones[$filtros->dimension]);
                        }

                        $filtrar = [$keyanio => $anio]; 
                        $almacenamiento->crearIndicador($fichaTec, $otros_filtros["dimension"], $filtrar);   
                                               
                        $statement = $connection->prepare("SELECT msd.mes, msd.planificado, msd.real, ms.meta FROM matriz_seguimiento ms 
                            LEFT JOIN matriz_seguimiento_dato msd ON msd.id_matriz = ms.id   
                            WHERE ms.anio = '$anio' and ms.etab = true and ms.id_desempeno = '".$value->id_desempeno."' and indicador = '".$indrs["id_ficha_tecnica"]."'");
                        $statement->execute();
                        $meses = $statement->fetchAll();
                        $meta = '';
                        if(isset($meses[0]))
                            $meta = $meses[0]["meta"];

                        $etab[$i] = array('id'=>$indrs["id_ficha_tecnica"], 'nombre'=>$indrs["nombre"],  'fuente' => ' eTAB', 'meta' => $meta);
                        $etab[$i]["alertas"] = $alertas;
                        $representa = 0; $id_siguiente = 0;
                        $errores = '';
                        $ttm = 0;
                        foreach ($meses as $km => $vm) {
                            $vm = (object) $vm;
                            if($vm->mes != 'fuente'){
                                $etab[$i][$vm->mes]["planificado"] = $vm->planificado;
                                $etab[$i][$vm->mes]["real"] = $vm->real;
                                                       
                                try{
                                    $ttm++; 
                                    // obtener datos de los indicadores de etab    
                                    $representa++;                                
                                    if($representa == $otros_filtros["representa"]){                                        
                                        $data_indicador = $this->obtenerDatosetab($ttm, $vm, $dimension, $keyanio, $anio, $fichaRepository, $fichaTec, $otros_filtros, $id_siguiente);
                                        if(count($data_indicador) == 0){
                                            $errores.= "<br>No se cargo linea: $ci mes: ".$vm->mes;
                                        }
                                        else{
                                            $measure = '';
                                            if(isset($data_indicador[0]))
                                                $measure = $data_indicador[0]["measure"];
                                            $etab[$i][$vm->mes]["real"] = $measure;
                                        }
                                        $id_siguiente++;
                                        $representa = 0; 
                                    }                                    
                                }
                                catch(\Exception $e){
                                    
                                } 
                            }                         
                        }
                        $i++;
                    }

                    $indicador['indicadores_etab'] = $etab;
                    $indicador['indicadores_relacion'] = $indicators;

                    $resp[] = $indicador;                    
                }
            }
            $resp = ["data" => $resp, "mensaje" => $this->get('translator')->trans('_cargado_correctamente'), "status" => 200];
        }else{
            $resp = ["data" => "false", "mensaje" => $this->get('translator')->trans('_ningun_dato_'), "status" => 404];
        }
        
        $response->setContent(json_encode($resp));

        return $response;
    }



    /**
     * @Route("/api/v1/matriz/configuracion", name="matriz_configuracion", methods={"GET"})
     * 
     * @SWG\Get(
     *      tags={"Matriz"},
     *      summary="Configuracion",
     *      description="Devuelve los de la matriz y en el año seleccionado",
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      @SWG\Parameter(parameter="a_in_path", name="anio", type="integer", in="query"),
     *      @SWG\Parameter(parameter="m_in_path", name="matrix", type="integer", in="query")
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="Regresa objecto"     
     *  ),
     * 
     * @SWG\Response(
     *     response=400,
     *     description="No se envio los parametros necesarios"     
     *  ),
     * 
     * @SWG\Response(
     *     response=404,
     *     description="El elemento no existe"     
     *  ),
     * 
     *  @SWG\Response(
     *     response=500,
     *     description="Regresa un error ocurrido en el servidor"     
     *  ),
     */
    public function matrizConfiguracion(Request $request){
        $response = new Response();

        $anio   = $request->query->get('anio');
        $matrix = $request->query->get('matrix');

        $em = $this->getDoctrine()->getEntityManager();

        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT distinct(ms.id_desempeno), mid.orden FROM matriz_seguimiento ms 
            LEFT JOIN matriz_indicadores_desempeno mid ON mid.id = ms.id_desempeno WHERE anio = '$anio'  and id_matriz = '$matrix' ORDER BY mid.orden ASC");
        $statement->execute();
        $matriz = $statement->fetchAll();

        if($matriz){
            $resp = array();
            foreach ($matriz as $key => $value) {
                $value = (object) $value;
                $ind = $em->getRepository(MatrizIndicadoresDesempeno::class)->find($value->id_desempeno);
                if($ind){
                    
                    $indicador = array();
                
                    $indicador['id'] = $ind->getId();
                    $indicador['nombre'] = $ind->getNombre();
                    
                    $indicators = array(); $i=0;
                    foreach($ind->getMatrizIndicadoresRelacion() as $indrs){
                        
                        $connection = $em->getConnection();
                        $statement = $connection->prepare("SELECT msd.mes, msd.planificado, msd.real, ms.meta FROM matriz_seguimiento ms 
                            LEFT JOIN matriz_seguimiento_dato msd ON msd.id_matriz = ms.id   
                            WHERE ms.anio = '$anio' and ms.etab = false and ms.id_desempeno = '".$value->id_desempeno."' and indicador = '".$indrs->getId()."'");
                        $statement->execute();
                        $meses = $statement->fetchAll();
                        $meta = '';
                        if(isset($meses[0]))
                            $meta = $meses[0]["meta"];
                        $indicators[$i] = array('id'=>$indrs->getId(), 'nombre'=>$indrs->getNombre(), 'fuente' => ' '.$indrs->getFuente(), 'meta' => $meta);

                        foreach ($meses as $km => $vm) {
                            $vm = (object) $vm; 
                            
                            if($vm->mes != 'fuente'){    
                                $indicators[$i][$vm->mes]["planificado"] = $vm->planificado;                            
                                $indicators[$i][$vm->mes]["real"] = $vm->real;                            
                            }
                        }
                        $i++;
                    }

                    $etab = array(); $i=0;
                    $fichaRepository = $em->getRepository(FichaTecnica::class);
                    foreach($ind->getMatrizIndicadoresEtab() as $indrs){                        
                        $fichaTec = $fichaRepository->find($indrs->getId());
                        $fichaRepository->crearIndicador($fichaTec);
                        
                        $connection = $em->getConnection();
                        $statement = $connection->prepare("SELECT msd.mes, msd.planificado, msd.real, ms.meta FROM matriz_seguimiento ms 
                            LEFT JOIN matriz_seguimiento_dato msd ON msd.id_matriz = ms.id   
                            WHERE ms.anio = '$anio' and ms.etab = true and ms.id_desempeno = '".$value->id_desempeno."' and indicador = '".$indrs->getId()."'");
                        $statement->execute();
                        $meses = $statement->fetchAll();
                        $meta = '';
                        if(isset($meses[0]))
                            $meta = $meses[0]["meta"];
                        $etab[$i] = array('id'=>$indrs->getId(), 'nombre'=>$indrs->getNombre(),  'fuente' => ' eTAB', 'meta' => $meta);
                        foreach ($meses as $km => $vm) {
                            $vm = (object) $vm;
                            if($vm->mes != 'fuente'){
                                $etab[$i][$vm->mes]["planificado"] = $vm->planificado;
                                $etab[$i][$vm->mes]["real"] = $vm->real;
                                                       
                                try{

                                    $filtros = ["mes" => strtoupper($vm->mes), "anio" => $anio];
                                    $fichaTec = $fichaRepository->find($indrs->getId());                                    
                                    $repFicha = $fichaRepository->calcularIndicador($fichaTec, "mes", $filtros, false, null, 1, false);
                                    
                                    if($repFicha){
                                        $measure = '';
                                        if(isset($repFicha[0]))
                                            $measure = $repFicha[0]["measure"];
                                        $etab[$i][$vm->mes]["real"] = $measure;
                                    }
                                    else{
                                        $etab[$i][$vm->mes]["real"] = $vm->real;
                                    }

                                }
                                catch(\Exception $e){
                                    
                                } 
                            }                         
                        }
                        $i++;
                    }

                    $indicador['indicadores_etab'] = $etab;
                    $indicador['indicadores_relacion'] = $indicators;

                    $resp[] = $indicador;                    
                }
            }
            $resp = ["data" => $resp, "mensaje" => $this->get('translator')->trans('_cargado_correctamente'), "status" => 200];
        }else{
            $resp = ["data" => "false", "mensaje" => $this->get('translator')->trans('_ningun_dato_'), "status" => 404];
        }
        
        $response->setContent(json_encode($resp));

        return $response;
    }

    private function obtenerDatosetab($ttm, $vm, $dimension, $keyanio, $anio, $fichaRepository, $fichaTec, $otros_filtros, $id_siguiente){
        // Buscar datos por el nombre del mes ejemplo ENERO y que corresponda a un catalogo (significado de campo)
        $filtrar = [$dimension => $this->obtenerIdCatalogo($dimension, strtoupper($vm->mes), $id_siguiente), $keyanio => $anio];                                                                       
        $data_indicador = $fichaRepository->calcularIndicador($fichaTec, $dimension, $filtrar, false, $otros_filtros, false);
        
        // Buscar datos por el nombre del mes concatenado con el indice ejemplo 01.Enero y que pertenesca a un catalogo (significado de campo)
        if(count($data_indicador) == 0){
            $filtrar = [$dimension => $this->obtenerIdCatalogo($dimension, str_pad($ttm, 2, "0", STR_PAD_LEFT).'.'.ucfirst($vm->mes), $id_siguiente), $keyanio => $anio];                                                                        
            $data_indicador = $fichaRepository->calcularIndicador($fichaTec, $dimension, $filtrar, false, $otros_filtros, false);                                         
        }  

        // Buscar datos por el nombre de la dimension mas el numero ejemplo Trimestre 1
        if(count($data_indicador) == 0){
            $filtrar = [$dimension => ucfirst($dimension).' '.($id_siguiente + 1), $keyanio => $anio]; 
            $data_indicador = $fichaRepository->calcularIndicador($fichaTec, $dimension, $filtrar, false, $otros_filtros, false);                                         
        }  

        // Buscar datos por el nombre del mes ejemplo ENERO
        if(count($data_indicador) == 0){
            $filtrar = [$dimension => strtoupper($vm->mes), $keyanio => $anio];                                                                       
            $data_indicador = $fichaRepository->calcularIndicador($fichaTec, $dimension, $filtrar, false, $otros_filtros, false);                                         
        }   
        // Buscar datos por el nombre la clave del mes ejemplo 1
        if(count($data_indicador) == 0){
            $filtrar = [$dimension => $ttm, $keyanio => $anio];                                                                                                                                                           
            $data_indicador = $fichaRepository->calcularIndicador($fichaTec, $dimension, $filtrar, false, $otros_filtros, false);                                         
        }
        // Buscar datos por el nombre del mes concatenado a la clave ejemplo 01.Enero
        if(count($data_indicador) == 0){
            $filtrar = [$dimension => str_pad($ttm, 2, "0", STR_PAD_LEFT).'.'.ucfirst($vm->mes), $keyanio => $anio];                                                                                                                     
            $data_indicador = $fichaRepository->calcularIndicador($fichaTec, $dimension, $filtrar, false, $otros_filtros, false);                                         
        }              
        
        return $data_indicador;
    }
    private function obtenerIdCatalogo($campo, $valor, $id_siguiente){
        $em = $this->getDoctrine()->getEntityManager();
        //Si el filtro es un catálogo, buscar su id correspondiente
        $significado = $em->getRepository(SignificadoCampo::class)
            ->findOneBy(array('codigo' => $campo));
        $catalogo = $significado->getCatalogo();

        if ($catalogo != '') {
            $sql_ctl = "SELECT id FROM $catalogo WHERE descripcion ='$valor'";
            $reg = $em->getConnection()->executeQuery($sql_ctl)->fetch();
            if(!$reg){
                $sql_ctl = "SELECT id FROM $catalogo WHERE descripcion like '%$valor%'";
                $reg = $em->getConnection()->executeQuery($sql_ctl)->fetch();
                if(!$reg){
                    $sql_ctl = "SELECT id FROM $catalogo ORDER BY id LIMIT 1 OFFSET ".$id_siguiente;
                    $reg = $em->getConnection()->executeQuery($sql_ctl)->fetch();
                    return $reg['id'];
                } else return $reg['id'];
                return $reg['id'];
            } else return $reg['id'];
        } else return null;
    }
}