<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

use App\AlmacenamientoDatos\AlmacenamientoProxy;

use App\Entity\FichaTecnica;
use App\Entity\ClasificacionUso;
use App\Entity\ClasificacionTecnica;
use App\Entity\User;
use App\Entity\GrupoIndicadores;
use App\Entity\GrupoIndicadoresIndicador;
use App\Entity\UsuarioGrupoIndicadores;

use App\Entity\SignificadoCampo;
use App\Entity\OrigenDatos;
use App\Entity\TipoGrafico;
use App\Entity\Social;


/**
 * @Route("/api/v1/tablero")
 */
class TableroController extends AbstractController {

    /**
     * @Route("/clasificacionUso", name="clasificacionUso_index", methods={"GET"})
     * 
     * @SWG\Get(
     *      tags={"Tablero"},
     *      summary="Clasificacion uso",
     *      description="Lista de clasificaciones uso",
     *      produces={"application/json"}
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
    public function clasificacionUso(Request $request){
       
        // iniciar el manager de doctrine
        $em = $this->getDoctrine()->getManager();
        
        // Si se está utilizando en una sala pública
        if ( $this->getUser() == null ){
            return new JsonResponse($response = [
                'status' => 200,
                'messages' => "Ok",
                'data' => [],
                'total' => 0
            ]);
        }
    
        try{ 
            $datos = (object) $request->query->all();           


            if ( $this->getUser()->hasRole('ROLE_SUPER_ADMIN') ) {
                // devolver todos los datos si lo requiere
                $repository = $this->getDoctrine()->getRepository(ClasificacionUso::class);

                $data = $repository->createQueryBuilder('p')
                    ->orderBy('p.descripcion', 'ASC')
                    ->getQuery()
                    ->getArrayResult();
            } else {
                //Devolver las clasificaciones de uso con indicadores del usuario
                $indicadores_permitidos = $this->getIndicadoresPermitidos();

                $data = [];
                foreach ($indicadores_permitidos as $ind ) {
                    if($ind){
                        foreach( $ind->getClasificacionTecnica() as $ct ) {
                            $cu = $ct->getClasificacionUso();
                            $data[ $cu->getId() ] = ['id' => $cu->getId(), 'codigo' => $cu->getCodigo(), 'descripcion' => $cu->getDescripcion() ];
                        }
                    }
                }

            }

            $total = count($data);
            
            // ejecutar el contenido de la memoria
            $em->flush();
            // validar que hay datos
            if($data){                
                $response = [
                    'status' => 200,
                    'messages' => "Ok",
                    'data' => $data,
                    'total' => $total
                ];                    
            } else{ 
                $response = [
                    'status' => 404,
                    'messages' => "Not Found",
                    'data' => [],
                ];
            }                        
        }catch(\Exception $e){
            $response = [
                'status' => 500,
                'messages' => $e->getMessage(),
                'data' => [],
            ];    
            
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/clasificacionTecnica", name="clasificacionTecnica_index", methods={"GET"})
     * 
     * @SWG\Get(
     *      tags={"Tablero"},
     *      summary="Clasificacion tecnica",
     *      description="Lista de clasificaciones tecnicas, filtrado por clasificacion uso",
     *      produces={"application/json"},
     *      @SWG\Parameter(parameter="id_in_path", name="id", description="id clasificacion uso", required=true, type="string", in="query")
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
    public function clasificacionTecnica(Request $request){
       
        // iniciar el manager de doctrine
        $em = $this->getDoctrine()->getManager();
        try{ 
            $datos = (object) $request->query->all();

            $stm = $em->getRepository(ClasificacionTecnica::class)
                        ->createQueryBuilder('ct')
                        ->where('ct.clasificacionUso = :usoId')
                        ->orderBy('ct.descripcion','ASC')
                        ->setParameter('usoId', $datos->id )

                    ;
            if ( !$this->getUser()->hasRole('ROLE_SUPER_ADMIN') ) {
                $indicadores_permitidos = $this->getIndicadoresPermitidos();
                $clasificacionesPermitidas = [];
                foreach ($indicadores_permitidos as $ind ) {
                    foreach( $ind->getClasificacionTecnica() as $ct ) {
                        array_push($clasificacionesPermitidas, $ct->getId() );
                    }
                }

                $stm
                    ->andWhere('ct.id IN (:ctPermitidas)')
                    ->setParameter('ctPermitidas', $clasificacionesPermitidas )
                    ;
            }

            $data = $stm->getQuery()->getArrayResult();

            $total = count($data);

            // validar que hay datos
            if($data){                
                $response = [
                    'status' => 200,
                    'messages' => "Ok",
                    'data' => $data,
                    'total' => $total
                ];                    
            } else{ 
                $response = [
                    'status' => 404,
                    'messages' => "Not Found",
                    'data' => [],
                ];
            }                        
        }catch(\Exception $e){
            $response = [
                'status' => 500,
                'messages' => $e->getMessage(),
                'data' => [],
            ];    
            
        }

        return new JsonResponse($response);

    }


    /**
     * @Route("/listaIndicadores", name="listaIndicadores_index", methods={"GET"})
     * 
     * @SWG\Get(
     *      tags={"Tablero"},
     *      summary="Lista de indicadores clasificados",
     *      description="Lista indicadores clasificados que pueden ser, favoritos, no_clasificados, clasificados",
     *      produces={"application/json"},  
     *      @SWG\Parameter(parameter="tipo_in_path", name="tipo", description="favoritos, no_clasificados, clasificados", required=true, type="string", in="query")
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
    public function listaIndicadores(Request $request){
       
        // Si se está utilizando en una sala pública
        if ( $this->getUser() == null ){
            return new JsonResponse($response = [
                'status' => 200,
                'messages' => "Ok",
                'data' => [],
                'total' => 0
            ]);
        }
        
        // iniciar el manager de doctrine
        $em = $this->getDoctrine()->getManager();
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        try{ 
            $usuario = $this->getUser();
            $datos = (object) $request->query->all();  
            $where = '';
            if (!$usuario->hasRole('ROLE_SUPER_ADMIN')) {
                $inds = $this->getIndicadoresPermitidos();
                $indicadores_permitidos = array_map( function ($ind){
                    return $ind->getId();
                }, $inds);

                if(count($indicadores_permitidos) > 0){
                    $indicadores_permitidos = implode(",", $indicadores_permitidos);
                    $where = "and id in($indicadores_permitidos)";
                }
            }

            // devolver todos los datos si lo requiere   
            $conn = $em->getConnection();
            if($datos->tipo == 'clasificados')
                $sql = "SELECT * FROM ficha_tecnica where id in(select fichatecnica_id from fichatecnica_clasificaciontecnica where clasificaciontecnica_id = $datos->tecnica) $where order by nombre; ";
            
            if($datos->tipo == 'no_clasificados')
                $sql = "SELECT * FROM ficha_tecnica where id not in(select fichatecnica_id from fichatecnica_clasificaciontecnica) $where order by nombre; ";

            if($datos->tipo == 'busqueda')
                $sql = "SELECT * FROM ficha_tecnica where nombre ilike '%".$datos->busqueda."%' $where order by nombre; ";

            if($datos->tipo == 'favoritos'){                
                $sql = "SELECT * FROM ficha_tecnica WHERE id in(SELECT id_indicador FROM usuario_indicadores_favoritos WHERE id_usuario =".$usuario->getId().") $where order by nombre; ";
            }

            $statement = $conn->prepare($sql);
            $statement->execute();
            $data = $statement->fetchAll();

            $total = count($data);
            
            // ejecutar el contenido de la memoria
            $em->flush();
            // validar que hay datos
            if($data){   
                foreach($data as $key => $value){ 
                    if(is_array($value))
                        $value = (object) $value;

                    $sql = "SELECT codigo, descripcion FROM tipo_grafico WHERE id in(SELECT tipografico_id FROM fichatecnica_tiposgraficos WHERE fichatecnica_id =".$value->id.")";
                    $statement = $conn->prepare($sql);
                    $statement->execute();
                    $graficos = $statement->fetchAll();
                   
                    if ($value->campos_indicador != '') {
                        $campos = explode(',', str_replace(array("'", ' '), array('', ''), $value->campos_indicador));
                    } else {
                        $campos = array();
                    }
                    $dimensiones = array();
                    foreach ($campos as $campo) {
                        $significado = $em->getRepository(SignificadoCampo::class)->findOneByCodigo($campo);
                        if (count($significado->getTiposGraficosArray()) > 0) {
                            $graficos_filtrados_ficha = [];                                         
                            if(count($graficos) > 0){                                                                                      
                                $graficos_filtrados_ficha = $graficos;
                            }else{
                                $graficos_filtrados_ficha = $significado->getTiposGraficosArray();
                            }
                            $dimensiones[$significado->getCodigo()]['graficos'] = $graficos_filtrados_ficha;
                        }
                    }
                    $data[$key]["dimensiones"] = $dimensiones;                 
                }                          
                $response = [
                    'status' => 200,
                    'messages' => "Ok",
                    'data' => $data,
                    'total' => $total
                ];                    
            } else{ 
                $response = [
                    'status' => 404,
                    'messages' => "Not Found",
                    'data' => [],
                ];
            }                        
        }catch(\Exception $e){
            $response = [
                'status' => 500,
                'messages' => $e->getMessage(),
                'data' => [],
            ];    
            
        }        
        // devolver la respuesta en json             
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @return array
     * Recupera un arreglo de indicadores a los cuales el usuario tiene permiso
     */
    public function getIndicadoresPermitidos() : array {
        $usuario = $this->getUser();
        $indicadores_permitidos = [];

        //Indicadores por usuario
        foreach ($usuario->getIndicadores() as $indicador) {
            array_push($indicadores_permitidos, $indicador);
        }
        //Salas por usuario
        foreach ($usuario->getGruposIndicadores() as $usrSala) {
            $sala = $usrSala->getGrupoIndicadores();
            foreach ($sala->getIndicadores() as $indicador) {
                array_push($indicadores_permitidos, $indicador->getIndicador());
            }

        }

        // Indicadores asignados al grupo al que pertenece el usuario
        foreach ($usuario->getGroups() as $grp) {
            foreach ($grp->getIndicadores() as $indicador) {
                //foreach ($usuario->getIndicadores() as $indicador) {
                array_push($indicadores_permitidos, $indicador);
                //}
            }
        }

        //Salas asignadas al grupo al que pertenece el usuario
        foreach ($usuario->getGroups() as $grp) {
            foreach ($grp->getSalas() as $sala) {
                foreach ($sala->getIndicadores() as $indicador) {
                    //foreach ($usuario->getIndicadores() as $indicador) {
                    array_push($indicadores_permitidos, $indicador->getIndicador());
                    //}
                }
            }
        }

        return $indicadores_permitidos;
    }

    /**
     * @Route("/listaSalas", name="listaSalas_index", methods={"GET"})
     * 
     * @SWG\Get(
     *      tags={"Tablero"},
     *      summary="Lista de salas",
     *      description="Lista indicadores salas permitidas para el usuario",
     *      produces={"application/json"},  
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
    public function listaSalas(Request $request){        
        
        // iniciar el manager de doctrine
        $em = $this->getDoctrine()->getManager();
        try{             
            $usuario = $this->getUser();
            $grupos = [];
            $where = '';
            if ( $this->getUser() !== null ){
                $usuarioId = $usuario->getId();
                $datos = (object) $request->query->all();                  
                $salas_permitidos = [];

                foreach ($usuario->getGruposIndicadores() as $sala) {
                    array_push($salas_permitidos, $sala->getGrupoIndicadores()->getId());
                }

                //Salas asignadas al grupo al que pertenece el usuario
                foreach ($usuario->getGroups() as $grp) {
                    foreach ($grp->getSalas() as $sala) {
                        array_push($salas_permitidos, $sala->getId());
                        array_push($grupos, $sala->getId());
                    }
                }
                array_unique($salas_permitidos);            

                if (count($salas_permitidos) > 0) {
                    $salas_permitidos = implode(",", $salas_permitidos);
                    $where = "and id in($salas_permitidos)";
                }
            } else {
                $usuarioId = -1;
                //Verificar si es una sala pública
                $token = $request->get('token');
                $idSala = $request->get('id');
                $where = " AND id = -1 ";
                if ( $token !== null and $idSala !== null ) {
                    $sa = $em->getRepository(Social::class)->getRuta($idSala,$token);                    
                    if ($sa and $sa != "Error") {
                        $where = " AND id = $idSala ";
                    }
                }
            }

            // devolver todos los datos si lo requiere   
            $conn = $em->getConnection();
            
            $sql = "SELECT * FROM grupo_indicadores where 1=1 $where order by nombre; ";
            
            $statement = $conn->prepare($sql);
            $statement->execute();
            $data = $statement->fetchAll();

            if(count($grupos) > 0){
                $grupos = implode(",", $grupos);
                $where = "$where and id in($grupos)";
            }

            $sql = "SELECT * FROM grupo_indicadores where 1=1 $where order by nombre; ";
            
            $statement = $conn->prepare($sql);
            $statement->execute();
            $salas_grupos = $statement->fetchAll();

            $sql = "SELECT g.* FROM usuario_grupo_indicadores u
            left join grupo_indicadores g on g.id = grupo_indicadores_id
            where u.es_duenio = true and u.usuario_id = ".$usuarioId." order by g.nombre; ";
            
            $statement = $conn->prepare($sql);
            $statement->execute();
            $salas_propias = $statement->fetchAll();

            
            // ejecutar el contenido de la memoria
            $em->flush();
            // validar que hay datos
            if($data){ 
                $data1 = []; $data2 = []; $data3 = [];
                foreach ($data as $key => $value) {                    
                    $sql = "SELECT id, indicador_id, grupo_indicadores_id, trim(dimension) AS dimension, filtro, filtro_posicion_desde, filtro_posicion_hasta, 
                                        filtro_elementos, posicion, tipo_grafico, vista, orden  
                         FROM grupo_indicadores_indicador
                    where grupo_indicadores_id = ".$value["id"]." order by posicion asc; ";
                    
                    $statement = $conn->prepare($sql);
                    $statement->execute();
                    $value["indicadores"] = $statement->fetchAll();  
                    array_push($data1, $value);                   
                } 
                foreach ($salas_grupos as $key => $value) {
                    $sql = "SELECT * FROM grupo_indicadores_indicador
                    where grupo_indicadores_id = ".$value["id"]." order by posicion asc; ";
                    
                    $statement = $conn->prepare($sql);
                    $statement->execute();
                    $value["indicadores"] = $statement->fetchAll();   
                    array_push($data2, $value);                   
                } 
                foreach ($salas_propias as $key => $value) {
                    $sql = "SELECT * FROM grupo_indicadores_indicador
                    where grupo_indicadores_id = ".$value["id"]." order by posicion asc; ";
                    
                    $statement = $conn->prepare($sql);
                    $statement->execute();
                    $value["indicadores"] = $statement->fetchAll();                     
                    array_push($data3, $value); 
                }                            
                $response = [
                    'status' => 200,
                    'messages' => "Ok",
                    'data' => $data1,
                    'salas_grupos' => $data2,
                    'salas_propias' => $data3,
                    'total' => count($data)
                ];                    
            } else{ 
                $response = [
                    'status' => 404,
                    'messages' => "Not Found",
                    'data' => [],
                ];
            }                        
        }catch(\Exception $e){
            $response = [
                'status' => 500,
                'messages' => $e->getMessage(),
                'data' => [],
            ];    
            
        }
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        // devolver la respuesta en json             
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Route("/datosIndicador/{id}/{dimension}", name="datosIndicador_index", methods={"POST", "GET"})
     * 
     * @SWG\Post(
     *      tags={"Tablero"},
     *      summary="Lista de indicadores clasificados",
     *      description="Lista indicadores clasificados que pueden ser, favoritos, no_clasificados, clasificados",
     *      produces={"application/json"},  
     *      @SWG\Parameter(parameter="ficha_in_path", name="id", description="id de la ficha tecnica", required=true, type="string", in="path"),
     *      @SWG\Parameter(parameter="dimension_in_path", name="dimension", description="Dimension a mostrar los datos", required=true, type="string", in="path"),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JSON con los filtros",
     *          type="object",
     *          format="application/json",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="filtros", type="string", example="", description="Objecto con los filtros"),
     *              @SWG\Property(property="tendencia", type="boolen", example=false, description="Bandera para mostrar tendencia"),
     *              @SWG\Property(property="ver_sql", type="boolean", example=false, description="Bandera para mostrar el sql")
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
    public function datosIndicador(FichaTecnica $fichaTec, $dimension, Request $request, AlmacenamientoProxy $almacenamiento){
       
        // iniciar el manager de doctrine
        $em = $this->getDoctrine()->getManager();
        $dimension = ( $dimension == 'null') ? null : trim($dimension);
        try{
            $datos = (object) $request->request->all(); 
            
            //Verificar si la petición a sido realizada con GET
            if ( $request->isMethod('get') ){
                $filtros_ = ( $request->get('filtros') == null ) ? [] : $request->get('filtros');
                $datos->filtros = [];
                foreach ($filtros_ as $f ){
                    $datos->filtros[] = json_decode($f);
                }

                $datos->ver_sql = ( $request->get('ver_sql') == null or $request->get('ver_sql') == 'false' ) ? false : $request->get('ver_sql');
                $datos->tendencia = ( $request->get('tendencia') == null or $request->get('tendencia') == 'false') ? false : true;
                if ( $request->get('otros_filtros') != null ) {
                    $datos->otros_filtros = $request->get('otros_filtros');
                }
            }

            if ($datos->filtros == null or $datos->filtros == '')
                $filtros = null;
            else{
                $filtros_dimensiones = [];
                $filtros_valores = [];
                $filtros = [];
                foreach ($datos->filtros as $f) 
                {  
                    if($f){
                        $f = (object) $f;
                        if(!empty($f->codigo))
                            $filtros[$f->codigo] = $f->valor;
                    }
                }                 
            }
            $otros_filtros = '';
            if(property_exists($datos,'otros_filtros')){
                $otros_filtros = $datos->otros_filtros;
            }
            if(!property_exists($datos,'tendencia')){
                $datos->tendencia = false;
            }
            $almacenamiento->crearIndicador($fichaTec, $dimension, $filtros);
            $data = $almacenamiento->calcularIndicador($fichaTec, $dimension, $filtros, $datos->ver_sql, $otros_filtros, $datos->tendencia);                        
            if($data){
                if($datos->tendencia ){
                    $info = []; $valores = []; 
                    $meses = [];
                    foreach ($data as $key => $value) {
                        if(is_array($value)) $value = (object) $value;
                        if(!array_key_exists($value->category, $info)){
                            $info[$value->category] = array(
                                "values" => [],
                                "key" => $value->category,
                                "category" => $value->category
                            );
                            $valores[$value->category] = [];
                        }
                        if($datos->tendencia){
                            $time = new \DateTime($value->fecha, new \DateTimeZone('America/Mexico_City'));
                            $time = $time->format('m');
                            $mes = $time;
                            array_push($valores[$value->category], array(
                                "x" => $time,    
                                "y" => ($value->measure) * 1, 
                                "z" => $value->anio,          
                                "category" => $value->category                
                            ));
                        }else{
                            $mes = ($key + 1);
                            array_push($valores[$value->category], array(
                                "x" => ($key + 1),    
                                "y" => ($value->measure) * 1,           
                                "category" => $value->category                
                            ));
                        }
                        
                        if(!in_array($mes, $meses))
                            array_push($meses, $mes);                       
                        $info[$value->category]["values"] = $valores[$value->category];
                    }
                    sort($meses);
                    $dataTemp = []; $unico = [];
                    foreach ($info as $key => $value) {
                        $valor = [];
                        foreach($meses as $item){
                            $existe = 0;
                            foreach($value["values"] as $val1){
                                
                                if($val1["x"] == $item){
                                    $val1["x"] = $val1["x"];
                                    array_push($valor, $val1);
                                    $existe = 1;
                                }
                            }
                            if($existe == 0){
                                array_push($valor, array(
                                    "x" => $item,    
                                    "y" => 0,  
                                    "z" => 'NA',         
                                    "category" => $value["values"][0]["category"]              
                                ));
                            }
                        }
                        $unico = array_merge($unico, $valor);
                        /*array_push($dataTemp, array(
                            "category"=> substr($value["values"][0]["fecha"], 0, 4),
                            "key"=> substr($value["values"][0]["fecha"], 0, 4),
                            "values" => $valor
                        ));*/
                    }
                    array_push($dataTemp, array(
                        "category"=> 'INDICADOR',
                        "key"=> 'INDICADOR',
                        "values" => $unico
                    ));
                } 
                $response = [
                    'status' => 200,
                    'messages' => "Ok",
                    'data' => $data                   
                ];  
                if($datos->tendencia )
                $response['data_tendencia'] = $dataTemp;
                if(!$datos->ver_sql){
                    $response['total'] = count($data);
                    $response['informacion'] = $this->dimensionIndicador($fichaTec);
                    $response['ficha'] = $this->fichaIndicador($fichaTec->getId(), true);
                }
            } else{ 
                $response = [
                    'status' => 404,
                    'messages' => "Not Found",
                    'data' => [],
                ];
            }       
        }catch(\Exception $e){
            $response = [
                'status' => 500,
                'messages' => $e->getMessage(),
                'data' => [],
            ];    
        }
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        // devolver la respuesta en json             
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Route("/dimensionIndicador/{id}", name="dimensionIndicador_index", methods={"GET"})
     */
    public function dimensionIndicador(FichaTecnica $fichaTec)
    {
        $resp = array();
        $em = $this->getDoctrine()->getManager();

        if ($fichaTec) {
            $resp['nombre_indicador'] = $fichaTec->getNombre();
            $resp['id_indicador'] = $fichaTec->getId();
            $resp['unidad_medida'] = $fichaTec->getUnidadMedida();
            $resp['meta'] = $fichaTec->getMeta();
            if ($fichaTec->getCamposIndicador() != '') {
                $campos = explode(',', str_replace(array("'", ' '), array('', ''), $fichaTec->getCamposIndicador()));
            } else {
                $campos = array();
            }
            $dimensiones = array();
            $conn = $em->getConnection();
            $sql = "SELECT codigo, descripcion FROM tipo_grafico WHERE id in(SELECT tipografico_id FROM fichatecnica_tiposgraficos WHERE fichatecnica_id =".$fichaTec->getId().")";
            $statement = $conn->prepare($sql);
            $statement->execute();
            $graficos = $statement->fetchAll();

            foreach ($campos as $campo) {
                $significado = $em->getRepository(SignificadoCampo::class)
                        ->findOneByCodigo($campo);
                if (count($significado->getTiposGraficosArray()) > 0) {
                    $dimensiones[$significado->getCodigo()]['descripcion'] = ucfirst(preg_replace('/^Identificador /i', '', $significado->getDescripcion()));
                    $dimensiones[$significado->getCodigo()]['escala'] = $significado->getEscala();
                    $dimensiones[$significado->getCodigo()]['origenX'] = $significado->getOrigenX();
                    $dimensiones[$significado->getCodigo()]['origenY'] = $significado->getOrigenY();
                    
                    $graficos_filtrados_ficha = [];
                    if(count($graficos) > 0){                                                                                      
                        $graficos_filtrados_ficha = $graficos;
                    }else{
                        $graficos_filtrados_ficha = $significado->getTiposGraficosArray();
                    }
                    $dimensiones[$significado->getCodigo()]['graficos'] = $graficos_filtrados_ficha;
                    $dimensiones[$significado->getCodigo()]['mapa'] = $significado->getNombreMapa();
                }
            }
            $rangos_alertas_aux = array();
            foreach ($fichaTec->getAlertas() as $k => $rango) {
                $rangos_alertas_aux[$rango->getLimiteSuperior()]['limite_sup'] = $rango->getLimiteSuperior();
                $rangos_alertas_aux[$rango->getLimiteSuperior()]['limite_inf'] = $rango->getLimiteInferior();
                $rangos_alertas_aux[$rango->getLimiteSuperior()]['color'] = $rango->getColor()->getCodigo();
                $rangos_alertas_aux[$rango->getLimiteSuperior()]['comentario'] = $rango->getComentario();
            }
            ksort($rangos_alertas_aux);
            $rangos_alertas = array();
            foreach ($rangos_alertas_aux as $rango) {
                $rangos_alertas[] = $rango;
            }
            $resp['rangos'] = $rangos_alertas;
            $resp['formula'] = $fichaTec->getFormula();
            $resp['dimensiones'] = $dimensiones;

            //Verificar que se tiene la más antigua de las últimas lecturas de los orígenes
            //de datos del indicador
            $ultima_lectura = null;
            
            foreach ($fichaTec->getVariables() as $var) {
                $fecha_lectura = $var->getOrigenDatos()->getUltimaActualizacion();
                if ($fecha_lectura > $ultima_lectura or $ultima_lectura == null) {
                    $ultima_lectura = $fecha_lectura;
                }
            }
            $usuario = $this->getUser();
            $es_favorito = false;
            if($usuario){
                $fav = "SELECT id_indicador FROM usuario_indicadores_favoritos WHERE id_usuario =".$usuario->getId()." and id_indicador = ".$fichaTec->getId();

                $conn = $em->getConnection();
                $fst = $conn->prepare($fav);
                $fst->execute();
                $favorito = $fst->fetchAll();

                foreach($favorito as $vfav){
                    if($vfav["id_indicador"] > 0)
                        $es_favorito = true;
                }
            }
            
            
            

            $resp['es_favorito'] = $es_favorito;

            $fichaTec->setUltimaLectura($ultima_lectura);
            //$em->flush();

            $d = $fichaTec->getUltimaLectura();
            if ($d)
                $resp['ultima_lectura'] = $d->format('d/m/Y');
            $resp['resultado'] = 'ok';
        } else {
            $resp['resultado'] = 'error';
        }      
        return $resp;
    }


    /**
     * @Route("/indicadorFavorito", name="indicadorFavorito", methods={"POST"})
     * 
     * @SWG\Post(
     *      tags={"Tablero"},
     *      summary="Favoritos",
     *      description="Agrega o quita un indicador a la lista de favoritos",
     *      produces={"application/json"},  
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JSON con el id a agregar",
     *          type="object",
     *          format="application/json",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="id", type="string", example="142", description="Id del indicador")
     *          )
     *      )
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="Regresa objecto"     
     *  ),
     */
    public function indicadorFavorito(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $req = $request;
        $favorito= false;
        $indicador = $em->find(FichaTecnica::class, $req->get('id'));
        $usuario = $this->getUser();
        if ($req->get('es_favorito') == 'true' || $req->get('es_favorito') == 1) {
            //Es favorito, entonces quitar
            $usuario->removeFavorito($indicador);
            $favorito= false;
        } else {
            $usuario->addFavorito($indicador);
            $favorito= true;
        }

        $em->flush();

        $response = [
            'status' => 200,
            'messages' => 'Ok',
            'data' => $favorito
        ];    
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Route("/fichaIndicador/{id}", name="fichaIndicador_index", methods={"GET"})
     * 
     * @SWG\Get(
     *      tags={"Tablero"},
     *      summary="Ficha indicador",
     *      description="Muestra la ficha tecnica del indicador",
     *      produces={"application/json"},  
     *      @SWG\Parameter(parameter="ficha_in_path", name="id", description="id de la ficha tecnica", required=true, type="string", in="path")      
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
    public function fichaIndicador($id, $respuesta = false){
        try{
            // iniciar el manager de doctrine
            $em = $this->getDoctrine()->getManager();
            // Consultar que el modulo exista
            $data = $em->getRepository(FichaTecnica::class)->find($id);
            
            // si existe el modulo
            if($data){
                $ultima_lectura = null;
                $variables = [];
                foreach ($data->getVariables() as $var) {
                    $fecha_lectura = $var->getOrigenDatos()->getUltimaActualizacion();
                    if ($fecha_lectura > $ultima_lectura or $ultima_lectura == null) {
                        $ultima_lectura = $fecha_lectura;
                    }
                    $origen = $var->getOrigenDatos();
                    $conexion = [];
                    foreach ($origen->getConexiones() as $con) {
                       array_push($conexion, array(
                                "id" => $con->getId(),
                                "nombre" => $con->getNombreConexion(),
                                "ip" => $con->getIp(),
                            )
                       );
                    }
                    array_push($variables, array(
                        "id" => $var->getId(),
                        "nombre" => $var->getNombre(),
                        "confiabilidad" => $var->getConfiabilidad(),
                        "iniciales" => $var->getIniciales(),
                        "comentario" => $var->getComentario(),
                        "es_poblacion" => $var->getEsPoblacion(),
                        "fuente_dato" => $var->getIdFuenteDato(),
                        "responsable_dato" => $var->getIdResponsableDato(),
                        "origen_dato" => array(
                            "id" => $origen->getId(),
                            "nombre" => $origen->getNombre(),
                            "conexion" => $conexion
                        )
                    ));
                }

                $alertas = [];
                foreach ($data->getAlertas() as $var) {                    
                    array_push($alertas, array(
                        "id" => $var->getId(),
                        "color" => $var->getColor(),
                        "limite_inf" => $var->getLimiteInferior(),
                        "limite_sup" => $var->getLimiteSuperior(),
                        "comentario" => $var->getComentario(),                    
                    ));
                }

                $clasificaciones = [];
                foreach ($data->getClasificacionTecnica() as $var) {                    
                    array_push($clasificaciones, array(
                        "id" => $var->getId(),
                        "descripcion" => $var->getDescripcion()                    
                    ));
                }
                
                $grupo = "SELECT * FROM grupo_indicadores_indicador WHERE indicador_id = ".$data->getId();

                $conn = $em->getConnection();
                $fst = $conn->prepare($grupo);
                $fst->execute();
                $reportes = $fst->fetchAll();

                $informacion = array(
                    "id" => $data->getId(),
                    "codigo" => $data->getcodigo(),
                    "nombre" => $data->getNombre(),
                    "tema" => $data->getTema(),
                    "concepto" => $data->getConcepto(),
                    "unidad_medida" => $data->getUnidadMedida(),
                    "formula" => $data->getFormula(),
                    "cantidad_decimales" => $data->getCantidadDecimales(),
                    "observacion" => $data->getObservacion(),                    
                    "ruta" => $data->getRuta(),
                    "dimensiones" => $data->getCamposIndicador(),
                    "confiabilidad" => $data->getConfiabilidad(),
                    "updated_at" => $data->getUpdatedAt()->format('d/m/Y'),
                    "es_acumulado" => $data->getEsAcumulado(),
                    "ultima_lectura" => $ultima_lectura ? $ultima_lectura->format('d/m/Y') : '',                    
                    "meta" => $data->getMeta(),
                    "periodo" => $data->getPeriodo(),
                    "reporte" => $reportes,
                    "clasificacion_tecnica" => $clasificaciones,
                    "alertas" => $alertas,
                    "variables" => $variables,
                );
                // ejecutar el contenido de la memoria
                $em->flush();
                    // devolver el mensaje en caso de que la contraseña no sea correcta
                $response = [
                    'status' => 200,
                    'messages' => "Ok",
                    'data' => $informacion
                ];                    
            } else{ // devolver el mensaje en caso de que el modulo no sea correcto
                $response = [
                    'status' => 404,
                    'messages' => "Not Found",
                    'data' => [],
                ];
            }
        }catch(\Exception $e){
            $response = [
                'status' => 500,
                'messages' => $e->getMessage(),
                'data' => [],
            ];                
        }
        if($respuesta){
            return $informacion;
        }
        else{
            $encoders = array(new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());

            $serializer = new Serializer($normalizers, $encoders);
            return new Response($serializer->serialize($response, "json"));
        }
    }

    //////////////////////// salas ///////////////////

    /**
     * @Route("/borrarSala", name="borrarSala_index", methods={"POST"})
     * 
     * @SWG\Post(
     *      tags={"Tablero"},
     *      summary="Borrar sala",
     *      description="Borra la sala seleccionada",
     *      produces={"application/json"},  
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JSON con el id a borrar",
     *          type="object",
     *          format="application/json",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="id", type="string", example="353", description="Objecto con los filtros")
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
    public function borrarSala(Request $request)
    {
        $lasala = new GrupoIndicadores();
        $em = $this->getDoctrine()->getManager();
        $req = $request;
        $resp = array();
        $em->getConnection()->beginTransaction();
        $sala = $req->get('id');
        try {
            if ($sala != '') 
            {
                $grupoIndicadores = $em->find(GrupoIndicadores::class, $sala);
                //Borrar los indicadores antiguos de la sala                
                foreach ($grupoIndicadores->getIndicadores() as $ind)
                    $em->remove($ind);                          
                    
                foreach ($grupoIndicadores->getUsuarios() as $ind)
                    $em->remove($ind);

                foreach ($grupoIndicadores->getGrupos() as $ind)
                    $em->remove($ind);

                foreach ($grupoIndicadores->getAcciones() as $ind)
                    $em->remove($ind);

                $connection = $em->getConnection();
                // usuarios asignados                    
                $statement = $connection->prepare("DELETE  FROM sala_acciones WHERE  grupo_indicadores_id = '" . $grupoIndicadores->getId() . "'");
                $statement->execute();
                $acciones = $statement->fetchAll();

                $statement = $connection->prepare("DELETE  FROM sala_comentarios WHERE  grupo_indicadores_id = '" . $grupoIndicadores->getId() . "'");
                $statement->execute();
                $comentarios = $statement->fetchAll();

                $statement = $connection->prepare("DELETE  FROM social WHERE  sala = '" . $grupoIndicadores->getId() . "'");
                $statement->execute();
                $social = $statement->fetchAll();

                $lasala=$em->find(GrupoIndicadores::class, $sala);
                $em->remove($lasala);
                    
                $em->flush();                               
         
                $em->getConnection()->commit();

                $response = [
                    'status' => 200,
                    'messages' => 'Ok',
                    'data' => []
                ];   
            } 
            else 
            {
                $response = [
                    'status' => 404,
                    'messages' => "Not Found",
                    'data' => [],
                ];
            }
        } 
        catch (\Exception $e) 
        {
            $response = [
                'status' => 500,
                'messages' => $e->getMessage(),
                'data' => [],
            ];    
        }

        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Route("/guardarSala", name="guardarSala_index", methods={"POST"})
     * 
     * @SWG\Post(
     *      tags={"Tablero"},
     *      summary="Guardar sala",
     *      description="Agrupa los indicadores por sala y los guarda",
     *      produces={"application/json"},  
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JSON con los filtros",
     *          type="object",
     *          format="application/json",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="sala", type="object", 
     *                  @SWG\Property(property="id", type="string", example=""),
     *                  @SWG\Property(property="nombre", type="string", example="Sala 1"),
     *              ),
     *              @SWG\Property(property="indicadores", type="array", 
     *                  @SWG\Items(
     *                      @SWG\Property(property="id", type="string", example="318"),
     *                      @SWG\Property(property="posicion", type="string", example="1"),
     *                      @SWG\Property(property="dimension", type="string", example="0"),
     *                      @SWG\Property(property="configuracion", type="object", 
     *                          @SWG\Property(property="height", type="string", example="280"),
     *                          @SWG\Property(property="maximo", type="string", example=""),
     *                          @SWG\Property(property="maximo_manual", type="string", example=""),
     *                          @SWG\Property(property="orden_x", type="string", example=""),
     *                          @SWG\Property(property="orden_y", type="string", example=""),
     *                          @SWG\Property(property="tipo_grafico", type="string", example="columnas"),
     *                          @SWG\Property(property="width", type="string", example="col-sm-4")
     *                      ),
     *                      @SWG\Property(
     *                          property="dimensiones",
     *                          type="array",
     *                          @SWG\Items(example="anio")
     *                      ),
     *                      @SWG\Property(
     *                           property="filtros",
     *                           type="array",
     *                           @SWG\Items(
     *                               @SWG\Property(
     *                                   property="codigo",
     *                                   type="string",
     *                                   example="anio"
     *                               ),@SWG\Property(
     *                                   property="etiqueta",
     *                                   type="string",
     *                                   example="Año"
     *                               ),@SWG\Property(
     *                                   property="valor",
     *                                   type="string",
     *                                   example="2016"
     *                               )
     *                           )
     *                      ),
     *                      @SWG\Property(
     *                           property="otros_filtros",
     *                           type="object",
     *                           @SWG\Property(
     *                               property="desde",
     *                               type="string",
     *                               example=""
     *                           ),@SWG\Property(
     *                               property="hasta",
     *                               type="string",
     *                               example=""
     *                           ),@SWG\Property(
     *                               property="elementos",
     *                               type="array",
     *                               @SWG\Items(example="")
     *                           )
     *                      ),
     *                  )
     *              ),              
     *          )
     *      )
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="Regresa objecto"     
     *  ),
     * 
     *  @SWG\Response(
     *     response=500,
     *     description="Regresa un error ocurrido en el servidor"     
     *  ),
     */
    public function guardarSala(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $req = (object) $request->request->all();
        $resp = array();
        $sala = $req->sala;
        $indicadores = $req->indicadores;
        $em->getConnection()->beginTransaction();
        
        try {
            if ($sala["id"] != '') {
                $grupoIndicadores = $em->find(GrupoIndicadores::class, $sala["id"]);
                foreach ($grupoIndicadores->getIndicadores() as $ind)
                    $em->remove($ind);
                $em->flush();
            } else {
                $grupoIndicadores = new GrupoIndicadores();
            }
            $grupoIndicadores->setNombre($sala["nombre"]);
            
            foreach ($indicadores as $grafico) {
                if(is_array($grafico))
                    $grafico = (object) $grafico;
                if (!empty($grafico->id)) {
                    $indG = new GrupoIndicadoresIndicador();
                    $ind = $em->find(FichaTecnica::class, $grafico->id);
                    if ( array_key_exists($grafico->dimension, $grafico->dimensiones) ) {
                        $indG->setDimension($grafico->dimensiones[$grafico->dimension]);
                    } else {
                        $indG->setDimension(trim($grafico->dimension));
                    }
                    
                    $indG->setFiltro(json_encode($grafico->filtros));
                    
                    if(property_exists($grafico, 'filtro_desde'))
                        $indG->setFiltroPosicionDesde($grafico->otros_filtros["desde"]);

                    if(property_exists($grafico, 'filtro_hasta'))
                        $indG->setFiltroPosicionHasta($grafico->otros_filtros["hasta"]);

                    $indG->setFiltroElementos(implode(",", $grafico->otros_filtros["elementos"]));
                    $indG->setIndicador($ind);
                    $indG->setPosicion($grafico->posicion);

                    if (property_exists($grafico, 'orden') or property_exists($grafico, 'configuracion')) {
                        $indG->setOrden(json_encode($grafico->configuracion));
                    }
                    $indG->setTipoGrafico($grafico->configuracion["tipo_grafico"]);
                    $indG->setGrupo($grupoIndicadores);
                    $grupoIndicadores->addIndicadore($indG);
                }
            }
            $em->persist($grupoIndicadores);
            $em->flush();
            if ($sala["id"] == '') {
                $usuarioGrupoIndicadores = new UsuarioGrupoIndicadores();
                $usuarioGrupoIndicadores->setUsuario($this->getUser());
                $usuarioGrupoIndicadores->setEsDuenio(true);
                $usuarioGrupoIndicadores->setGrupoIndicadores($grupoIndicadores);
                $em->persist($usuarioGrupoIndicadores);
                $em->flush();
            }
            $response = [
                'status' => 200,
                'messages' => 'Ok',
                'data' => $grupoIndicadores->getId()
            ];   

            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $response = [
                'status' => 500,
                'messages' => $e->getMessage(),
                'data' => [],
            ];   
        }

        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        return new Response($serializer->serialize($response, "json"));
    }
    protected $tamanio = 50000; 
    /**
     * @Route("/datosPivot/{id}", name="pivot_index", methods={"POST", "GET"})
     */
    public function getPivot(FichaTecnica $fichaTec, Request $request, AlmacenamientoProxy $almacenamiento) {
        // iniciar el manager de doctrine
        $em = $this->getDoctrine()->getManager();
        try{
            $datos = (object) $request->request->all(); 
           
            $totalRegistros = $almacenamiento->totalRegistrosIndicador($fichaTec);
            $almacenamiento->crearIndicador($fichaTec);
            $data = $almacenamiento->getDatosIndicador($fichaTec, 0, $this->tamanio);
                        
            if($data){   
                $response = [
                    'status' => 200,
                    'messages' => "Ok",
                    'data' => $data                   
                ];  
                if(!$datos->ver_sql){
                    $response['total'] = count($data);
                    $response['informacion'] = $this->dimensionIndicador($fichaTec);
                    $response['total_partes'] = ceil($totalRegistros / $this->tamanio );
                }
            } else{ 
                $response = [
                    'status' => 404,
                    'messages' => "Not Found",
                    'data' => [],
                ];
            }       
        }catch(\Exception $e){
            $response = [
                'status' => 500,
                'messages' => $e->getMessage(),
                'data' => [],
            ];    
        }
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        // devolver la respuesta en json             
        return new Response($serializer->serialize($response, "json"));
        
    }

    /**
     * @Route("/mapa/{dimension}", name="mapa", methods={"GET"})
     */
    public function getMapaAction($dimension, Request $request) {
        $em = $this->getDoctrine()->getManager();
        
        //Obtener el nombre del mapa asociado a la dimension
        $significado = $em->getRepository(SignificadoCampo::class)
                ->findOneBy(array('codigo' => $dimension));

        $mapa = $significado->getNombreMapa();
         if ($mapa != '') {
            try {
                $mapa = $this->renderView('Indicador/' . $mapa . '.json.twig');
            } catch (\Exception $e) {
                $mapa = json_encode(array('features' => ''));
            }
        } else
            $mapa = json_encode(array('features' => ''));                                     
        
        $headers = array('accept-ranges' => 'bytes',
        'access-control-allow-origin' => '*',
        'cache-control' => 'max-age=300',
        'connection' => 'keep-alive',
        'content-length' => '656673',
        'content-security-policy' =>"'default-src 'none'; style-src 'unsafe-inline'; sandbox",
        'content-type' => 'text/plain; charset=utf-8',
        'date' => 'Mon, 11 Feb 2019 23:15:26 GMT',
        'etag' => '"c899e3d4f3353924e495667c842f54a07090cfab"',
        'expires' => 'Mon, 11 Feb 2019 23:20:26 GMT',
        'source-age' => '40',
        'strict-transport-security' => 'max-age=31536000',
        'vary' => 'Authorization,Accept-Encoding',
        'via' => '1.1 varnish',
        'x-cache' => 'HIT',
        'x-cache-hits' => '1',
        'x-content-type-options' => 'nosniff',
        'x-fastly-request-id' => '2d3d76de56dbc00b0c93e1ed0501e54efe8090bb',
        'x-frame-options' => 'deny',
        'x-geo-block-list' => '');
        $response = new Response($mapa, 200, $headers);        

        return $response;    
    }

    /**
     * @Route("/datosCatalogo/{codigoDimension}", name="datosCatalogo_index", methods={"GET"})
     *
     * @SWG\Get(
     *      tags={"Tablero"},
     *      summary="Datos de catálogos",
     *      description="Lista de valores de los campos que son catálogo",
     *      produces={"application/json"}
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
    public function datosCatalogo(Request $request, $codigoDimension){

        $em = $this->getDoctrine()->getManager();

        try{

            $significado = $em->getRepository(SignificadoCampo::class)
                ->findOneBy( ['codigo' => $codigoDimension] );

            $catalogo = $significado->getCatalogo();

            if ($catalogo != '') {
                $sql_ctl = "SELECT id, descripcion FROM $catalogo ORDER BY descripcion LIMIT 500";
                $data = $em->getConnection()->executeQuery($sql_ctl)->fetchAll();
            }

            $total = count($data);

            // validar que hay datos
            if( $total > 0 ){
                $response = [
                    'status' => 200,
                    'messages' => "Ok",
                    'data' => $data,
                    'total' => $total
                ];
            } else{
                $response = [
                    'status' => 404,
                    'messages' => "Not Found",
                    'data' => [],
                ];
            }
        }catch(\Exception $e){
            $response = [
                'status' => 500,
                'messages' => $e->getMessage(),
                'data' => [],
            ];

        }

        return new JsonResponse($response);
    }
}
