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


/**
 * @Route("/api/v1/tablero")
 */
class TableroController extends AbstractController {

    /**
     * @Route("/clasificacionUso", name="clasificacionUso_index", methods={"GET"})
     */
    public function clasificacionUso(Request $request){
       
        // iniciar el manager de doctrine
        $em = $this->getDoctrine()->getManager();
        try{ 
            $datos = (object) $request->query->all();           

            // devolver todos los datos si lo requiere             
            $repository = $this->getDoctrine()->getRepository(ClasificacionUso::class);
                    
            $query = $repository->createQueryBuilder('p')  
                ->orderBy('p.descripcion', 'ASC')
                ->getQuery();

            $data = $query->getResult();

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
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        // devolver la respuesta en json             
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Route("/clasificacionTecnica", name="clasificacionTecnica_index", methods={"GET"})
     */
    public function clasificacionTecnica(Request $request){
       
        // iniciar el manager de doctrine
        $em = $this->getDoctrine()->getManager();
        try{ 
            $datos = (object) $request->query->all();           

            // devolver todos los datos si lo requiere    
            /*$conn = $em->getConnection();
            
            $sql = "SELECT * FROM clasificacion_tecnica where clasificacionuso_id = :id order by descripcion ";
            
            $statement = $conn->prepare($sql);
            $statement->bindValue('id', $datos->id);
            $statement->execute();
            $data = $statement->fetchAll();*/


            $data = $em->getRepository(ClasificacionTecnica::class)
                        ->createQueryBuilder('ct')
                        ->where('ct.clasificacionUso = :usoId')
                        ->orderBy('ct.descripcion','ASC')
                        ->setParameter('usoId', $datos->id )
                        ->getQuery()
                        ->getArrayResult()
                    ;

            $total = count($data);
            
            // ejecutar el contenido de la memoria
            // $em->flush();
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
        /*$encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        // devolver la respuesta en json             
        return new Response($serializer->serialize($response, "json"));*/
        return new JsonResponse($response);

    }


    /**
     * @Route("/listaIndicadores", name="listaIndicadores_index", methods={"GET"})
     */
    public function listaIndicadores(Request $request){
       
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
                $indicadores_permitidos = [];

                //Indicadores por usuario
                foreach ($usuario->getIndicadores() as $indicador) {                    
                    array_push($indicadores_permitidos, $indicador->getId());
                }
                //Salas por usuario
                foreach ($usuario->getGruposIndicadores() as $sala) {                    
                    foreach ($sala->getGrupoIndicadores() as $indicador) { 
                        foreach ($usuario->getIndicadores() as $indicador) {                    
                            array_push($indicadores_permitidos, $indicador->getId());
                        }
                    }
                }
                //Salas asignadas al grupo al que pertenece el usuario
                foreach ($usuario->getGroups() as $grp) {
                    foreach ($grp->getSalas() as $sala) {
                        foreach ($sala->getGrupoIndicadores() as $indicador) { 
                            foreach ($usuario->getIndicadores() as $indicador) {                    
                                array_push($indicadores_permitidos, $indicador->getId());
                            }
                        }
                    }
                }
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
                $sql = "SELECT * FROM ficha_tecnica where nombre like '%".$datos->busqueda."%' $where order by nombre; ";

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
     * @Route("/listaSalas", name="listaSalas_index", methods={"GET"})
     */
    public function listaSalas(Request $request){
       
        // iniciar el manager de doctrine
        $em = $this->getDoctrine()->getManager();
        try{ 
            $usuario = $this->getUser();
            $datos = (object) $request->query->all();  
            $where = '';
            if ($usuario->hasRole('ROLE_SUPER_ADMIN')) {
                $salas_permitidos = [];

                foreach ($usuario->getGruposIndicadores() as $sala) {
                    array_push($salas_permitidos, $sala->getGrupoIndicadores()->getId());
                }
                
                //Salas asignadas al grupo al que pertenece el usuario
                $grupos = [];
                foreach ($usuario->getGroups() as $grp) {
                    foreach ($grp->getSalas() as $sala) {
                        array_push($salas_permitidos, $sala->getId());
                        array_push($grupos, $sala->getId());
                    }
                }

                if(count($salas_permitidos) > 0){
                    $salas_permitidos = implode(",", $salas_permitidos);
                    $where = "and id in($salas_permitidos)";
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
            where u.es_duenio = true and u.usuario_id = ".$usuario->getId()." order by g.nombre; ";
            
            $statement = $conn->prepare($sql);
            $statement->execute();
            $salas_propias = $statement->fetchAll();

            
            // ejecutar el contenido de la memoria
            $em->flush();
            // validar que hay datos
            if($data){ 
                $data1 = []; $data2 = []; $data3 = [];
                foreach ($data as $key => $value) {                    
                    $sql = "SELECT * FROM grupo_indicadores_indicador
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
     * @Route("/datosIndicador/{id}/{dimension}", name="datosIndicador_index", methods={"POST"})
     */
    public function datosIndicador(FichaTecnica $fichaTec, $dimension, Request $request, AlmacenamientoProxy $almacenamiento){
       
        // iniciar el manager de doctrine
        $em = $this->getDoctrine()->getManager();
        try{
            $datos = (object) $request->request->all(); 

            if ($datos->filtros == null or $datos->filtros == '')
                $filtros = null;
            else{
                $filtros_dimensiones = [];
                $filtros_valores = [];
                $filtros = [];
                foreach ($datos->filtros as $f) 
                {  
                    $f = (object) $f;
                    $filtros[$f->codigo] = $f->valor;
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
                if($datos->tendencia){
                    $info = []; $valores = [];
                    foreach ($data as $key => $value) {
                        if(is_array($value)) $value = (object) $value;
                        if(!array_key_exists($value->category, $info)){
                            $info[$value->category] = array(
                                "values" => [],
                                "key" => $value->category
                            );
                            $valores[$value->category] = [];
                        }
                        $time = new \DateTime($value->fecha.'-01', new \DateTimeZone('America/Mexico_City'));
                        array_push($valores[$value->category], array(
                            "x" => $time->getTimestamp() * 1000,  // Fecha en milisegundos
                            "y" => ($value->measure) * 1,      // Valor 
                            "fecha" => $value->fecha              // fecha del evento opcional 
                        ));
                        $info[$value->category]["values"] = $valores[$value->category];
                    }
                    $dataTemp = [];
                    foreach ($info as $key => $value) {
                        array_push($dataTemp, $value);
                    }
                    $data = $dataTemp;
                } 
                $response = [
                    'status' => 200,
                    'messages' => "Ok",
                    'data' => $data                    
                ];  
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
            $fav = "SELECT id_indicador FROM usuario_indicadores_favoritos WHERE id_usuario =".$usuario->getId()." and id_indicador = ".$fichaTec->getId();

            $conn = $em->getConnection();
            $fst = $conn->prepare($fav);
            $fst->execute();
            $favorito = $fst->fetchAll();
            $es_favorito = false;
            
            foreach($favorito as $vfav){
                if($vfav["id_indicador"] > 0)
                    $es_favorito = true;
            }

            $resp['es_favorito'] = $es_favorito;

            $fichaTec->setUltimaLectura($ultima_lectura);
            //$em->flush();

            $d = $fichaTec->getUltimaLectura();
            if ($d !== false)
                $resp['ultima_lectura'] = $d->format('d/m/Y');
            $resp['resultado'] = 'ok';
        } else {
            $resp['resultado'] = 'error';
        }      
        return $resp;
    }


    /**
     * @Route("/indicadorFavorito", name="indicadorFavorito", methods={"POST"})
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

                $informacion = array(
                    "id" => $data->getId(),
                    "codigo" => $data->getcodigo(),
                    "nombre" => $data->getNombre(),
                    "tema" => $data->getTema(),
                    "concepto" => $data->getConcepto(),
                    "unidad_medida" => $data->getUnidadMedida(),
                    "formula" => $data->getFormula(),
                    "observacion" => $data->getObservacion(),                    
                    "ruta" => $data->getRuta(),
                    "dimensiones" => $data->getCamposIndicador(),
                    "confiabilidad" => $data->getConfiabilidad(),
                    "updated_at" => $data->getUpdatedAt()->format('d/m/Y'),
                    "es_acumulado" => $data->getEsAcumulado(),
                    "ultima_lectura" => $ultima_lectura->format('d/m/Y'),                    
                    "meta" => $data->getMeta(),
                    "periodo" => $data->getPeriodo(),
                    "reporte" => $data->getReporte(),
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
                    $indG->setDimension($grafico->dimensiones[$grafico->dimension]);
                    $indG->setFiltro(json_encode($grafico->filtros));
                    
                    if(property_exists($grafico, 'filtro_desde'))
                        $indG->setFiltroPosicionDesde($grafico->otros_filtros["desde"]);

                    if(property_exists($grafico, 'filtro_hasta'))
                        $indG->setFiltroPosicionHasta($grafico->otros_filtros["hasta"]);

                    $indG->setFiltroElementos(implode(",", $grafico->otros_filtros["elementos"]));
                    $indG->setIndicador($ind);
                    $indG->setPosicion($grafico->posicion);

                    if (property_exists($grafico, 'orden')) {
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
}
