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

use App\AlmacenamientoDatos\AlmacenamientoProxy;

use App\Entity\FichaTecnica;
use App\Entity\ClasificacionUso;
use App\Entity\ClasificacionTecnica;
use App\Entity\User;
use App\Entity\GrupoIndicadores;
use App\Entity\UsuarioGrupoIndicadores;
use App\Entity\SignificadoCampo;
use App\Entity\OrigenDatos;
use App\Entity\GrupoIndicadoresIndicador;


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
            $conn = $em->getConnection();
            
            $sql = "SELECT * FROM clasificacion_uso where id = :id order by descripcion; ";
            
            $statement = $conn->prepare($sql);
            $statement->bindValue('id', $datos->id);
            $statement->execute();
            $data = $statement->fetchAll();

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
     * @Route("/listaIndicadores", name="listaIndicadores_index", methods={"GET"})
     */
    public function listaIndicadores(Request $request){
       
        // iniciar el manager de doctrine
        $em = $this->getDoctrine()->getManager();
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
                $favoritos = [];
                foreach ($usuario->getFavoritos() as $fav) {                    
                    array_push($favoritos, $fav->getId());
                }
                if(count($favoritos) > 0){
                    $favoritos = implode(",", $favoritos);
                    $where = "where id in($favoritos) $where";
                }
                
                $sql = "SELECT * FROM ficha_tecnica $where order by nombre; ";
            }
            
            $statement = $conn->prepare($sql);
            $statement->execute();
            $data = $statement->fetchAll();

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
                $response = [
                    'status' => 200,
                    'messages' => "Ok",
                    'data' => $data,
                    'salas_grupos' => $salas_grupos,
                    'salas_propias' => $salas_propias,
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
                    array_push($filtros, [$f->codigo => $f->valor]);
                } 
            }

            $almacenamiento->crearIndicador($fichaTec, $dimension, $filtros);
            $data = $almacenamiento->calcularIndicador($fichaTec, $dimension, $filtros, $datos->ver_sql);
                        
            if($data){   
                $response = [
                    'status' => 200,
                    'messages' => "Ok",
                    'data' => $data,
                    'informacion' => $this->dimensionIndicador($fichaTec),
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
            foreach ($campos as $campo) {
                $significado = $em->getRepository(SignificadoCampo::class)
                        ->findOneByCodigo($campo);
                if (count($significado->getTiposGraficosArray()) > 0) {
                    $dimensiones[$significado->getCodigo()]['descripcion'] = ucfirst(preg_replace('/^Identificador /i', '', $significado->getDescripcion()));
                    $dimensiones[$significado->getCodigo()]['escala'] = $significado->getEscala();
                    $dimensiones[$significado->getCodigo()]['origenX'] = $significado->getOrigenX();
                    $dimensiones[$significado->getCodigo()]['origenY'] = $significado->getOrigenY();
                    $dimensiones[$significado->getCodigo()]['graficos'] = $significado->getTiposGraficosArray();
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
}

//end class
