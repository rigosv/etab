<?php

namespace App\Controller\MatrizChiapas;

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

/**
 * @Route("/api/v1/matriz")
 */
class MatrizRESTController extends AbstractController {

    /**
     * @Route("/listaUsuarios", name="listaUsuarios", methods={"GET"})
     */
    public function listaUsuarios(Request $request){
       
        // iniciar el manager de doctrine
        $em = $this->getDoctrine()->getManager();
        try{ 
            $datos = (object) $request->query->all();

            $stm = $em->getRepository(User::class)
                        ->createQueryBuilder('ct')
                        ->orderBy('ct.lastname','ASC')
                        ->orderBy('ct.username','ASC');
           
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
     * @Route("/listaColores", name="listaColores", methods={"GET"})
     */
    public function listaColores(Request $request){
       
        // iniciar el manager de doctrine
        $em = $this->getDoctrine()->getManager();

        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        try{ 
            $datos = (object) $request->query->all();

            $stm = $em->getRepository(Alerta::class)
                        ->createQueryBuilder('ct')
                        ->orderBy('ct.color','ASC');
           
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

       return new Response($serializer->serialize($response, "json"));

    }

     /**
     * @Route("/MatrizConfiguracion", name="MatrizConfiguracion", methods={"GET"})
     */
    public function index(Request $request){
    }

    /**
     * @Route("/MatrizConfiguracion", name="MatrizConfiguracion_store", methods={"POST"})
     */
    public function store(Request $request){
        // iniciar para la repuesta en formato json
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        try{            
            // obtener los datos de entrada y convertirlos a object es mas facil su acceso
            $datos = (object) $request->request->all(); 

            // iniciar el manager de doctrine
            $em = $this->getDoctrine()->getManager();
            // Consultar que el modulo exista
            $data = new MatrizSeguimientoMatriz();
            
            // funcion para el tratado del los datos de entrada                    
            $response = $this->formulario($data, $datos, $em, 1);   
            if($response["status"] == 200)
                $response["status"] = 201;                                                     
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
     * @Route("/MatrizConfiguracion/{id}", name="MatrizConfiguracion_update", methods={"PUT"})
     */
    public function update($id, Request $request){
        // iniciar para la repuesta en formato json
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        try{
            // validar que ningun dato est vacio
            if(isset($id) &&  $id != ''){
                // obtener los datos de entrada y convertirlos a object es mas facil su acceso
                $datos = (object) $request->request->all(); 

                // iniciar el manager de doctrine
                $em = $this->getDoctrine()->getManager();
                // Consultar que el modulo exista
                $data = $em->getRepository(MatrizSeguimientoMatriz::class)->find($id);
                // si existe el modulo
                if($data){
                    // funcion para el tratado del los datos de entrada                    
                    $response = $this->formulario($data, $datos, $em, 2);                   
                } else{ // devolver el mensaje en caso de que el modulo no sea correcto
                    $response = [
                        'status' => 404,
                        'messages' => "Not Found",
                        'data' => [],
                    ];
                }
            }else{
                $response = [
                    'status' => 400,
                    'messages' => "Bad Request",
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

    private function formulario($data, $datos, $em, $tipo){
        try{
            if(property_exists($datos, 'nombre'))
                $data->setNombre($datos->nombre);
            
            if(property_exists($datos, 'descripcion'))
                $data->setDescripcion($datos->descripcion);            
                        
            // continuar si se guarda correctamente
            $em->persist($data);
            // ejecutar el contenido de la memoria
            $em->flush();

            if(property_exists($datos, "indicadores_desempeno")){
                $existe_desemepno = array();     
                foreach($datos->indicadores_desempeno as $clave => $valor){ 
                    if(is_array($valor)) {
                        $valor = (object) $valor;
                    }
                    if(property_exists($valor, 'id') && $tipo == 2){
                        $desempeno = $em->getRepository(MatrizIndicadoresDesempeno::class)->find($valor->id);
                    } else{
                        $desempeno = new MatrizIndicadoresDesempeno();
                    }

                    $desempeno->setMatriz($em->getRepository(MatrizSeguimientoMatriz::class)->find($data->getId()));

                    if(property_exists($valor, 'nombre'))
                        $desempeno->setNombre($valor->nombre);
                    
                    if(property_exists($valor, 'orden'))
                        $desempeno->setOrden($valor->orden);
                        

                    $em->persist($desempeno); 

                    $em->flush();

                    array_push($existe_desemepno ,$desempeno->getId());

                    // guardar los indicadores no etab
                    if(property_exists($valor, "indicador_relacion")){
                        $existe_relacion = array();     
                        foreach($valor->indicador_relacion as $clave1 => $valor1){ 
                            if(is_array($valor1)) {
                                $valor1 = (object) $valor1;
                            }
                            if(property_exists($valor1, 'id') && $tipo == 2){
                                $relacion = $em->getRepository(MatrizIndicadoresRel::class)->find($valor1->id);
                            } else{
                                $relacion = new MatrizIndicadoresRel();
                            }

                            $relacion->setDesempeno($em->getRepository(MatrizIndicadoresDesempeno::class)->find($desempeno->getId()));

                            if(property_exists($valor1, 'nombre'))
                                $relacion->setNombre($valor1->nombre);
                            
                            if(property_exists($valor1, 'fuente'))
                                $relacion->setFuente($valor1->fuente);
                                

                            $em->persist($relacion); 

                            $em->flush();

                            array_push($existe_relacion ,$relacion->getId());

                            // guardar las alertas
                            if(property_exists($valor1, "alertas")){
                                $existe_alertas = array();     
                                foreach($valor1->alertas as $clave2 => $valor2){ 
                                    if(is_array($valor2)) {
                                        $valor2 = (object) $valor2;
                                    }
                                    if(property_exists($valor2, 'id') && $tipo == 2){
                                        $alerta = $em->getRepository(MatrizIndicadoresRelAlertas::class)->find($valor2->id);
                                    } else{
                                        $alerta = new MatrizIndicadoresRelAlertas();
                                    }

                                    $alerta->setMatrizIndicador($em->getRepository(MatrizIndicadoresRel::class)->find($relacion->getId()));

                                    if(property_exists($valor2, 'limite_inferior'))
                                        $alerta->setLimiteInferior($valor2->limite_inferior);
                                    
                                    if(property_exists($valor2, 'limite_superior'))
                                        $alerta->setLimiteSuperior($valor2->limite_superior);
                                    
                                    if(property_exists($valor2, 'color'))
                                        $alerta->setColor(json_encode($valor2->color));
                                        
                                    $em->persist($alerta); 

                                    $em->flush();

                                    array_push($existe_alertas ,$alerta->getId());
                                }
                                
                                // borra las que no esten dentro del array que envio el usuario                  
                                if(count($existe_alertas) > 0){ 
                                    $existe_alertas = implode(",", $existe_alertas);
                                    $sql = "DELETE FROM matriz_indicadores_relacion_alertas  WHERE id not in($existe_alertas) and matriz_indicador_relacion_id = ".$relacion->getId();
                                    $statement = $em->getConnection()->prepare($sql);
                                    $statement->execute();   
                                }
                                
                            }
                        }
                        
                        // borra las que no esten dentro del array que envio el usuario                  
                        if(count($existe_relacion) > 0){ 
                            $existe_relacion = implode(",", $existe_relacion);
                            $sql = "DELETE FROM matriz_indicadores_relacion  WHERE id not in($existe_relacion) and id_desempeno = ".$desempeno->getId();
                            $statement = $em->getConnection()->prepare($sql);
                            $statement->execute();   
                        }
                        
                    }

                    // guardar los indicadores etab
                    if(property_exists($valor, "indicador_etab")){
                        $existe_etab = array();     
                        foreach($valor->indicador_etab as $clave1 => $valor1){ 
                            if(is_array($valor1)) {
                                $valor1 = (object) $valor1;
                            }
                            if(property_exists($valor1, 'id') && $tipo == 2){
                                $etab = $em->getRepository(MatrizIndicadoresEtab::class)->find($valor1->id);
                            } else{
                                $etab = new MatrizIndicadoresEtab();
                            }

                            $etab->setDesempeno($em->getRepository(MatrizIndicadoresDesempeno::class)->find($desempeno->getId()));
                            $etab->setFicha($em->getRepository(FichaTecnica::class)->find($valor1->indicador));
                                                          
                            $filrt = [];
                            if(property_exists($valor1, 'filtros'))
                            foreach($valor1->filtros as $fil){
                                array_push($filrt, array(
                                    "codigo" => $fil["codigo"],
                                    "valor" => $fil["valor"]
                                ));
                            }
                            $filtros = array(
                                "dimensiones" =>$valor1->dimensiones,
                                "dimension" =>$valor1->dimension,
                                "filtros" => $filrt,
                                "otros_filtros" => $valor1->otros_filtros
                            );
                            $filtros = (object) $filtros;
                            $etab->setFiltros(json_encode($filtros));                      

                            $em->persist($etab); 

                            $em->flush();

                            array_push($existe_etab ,$etab->getId());

                            // guardar las alertas
                            if(property_exists($valor1, "alertas")){
                                $existe_alertas = array();     
                                foreach($valor1->alertas as $clave2 => $valor2){ 
                                    if(is_array($valor2)) {
                                        $valor2 = (object) $valor2;
                                    }
                                    if(property_exists($valor2, 'id') && $tipo == 2){
                                        $alerta = $em->getRepository(MatrizIndicadoresEtabAlertas::class)->find($valor2->id);
                                    } else{
                                        $alerta = new MatrizIndicadoresEtabAlertas();
                                    }

                                    $alerta->setMatrizIndicador($em->getRepository(MatrizIndicadoresEtab::class)->find($etab->getId()));

                                    if(property_exists($valor2, 'limite_inferior'))
                                        $alerta->setLimiteInferior($valor2->limite_inferior);
                                    
                                    if(property_exists($valor2, 'limite_superior'))
                                        $alerta->setLimiteSuperior($valor2->limite_superior);
                                    
                                    if(property_exists($valor2, 'color'))
                                        $alerta->setColor(json_encode($valor2->color));
                                        
                                    $em->persist($alerta); 

                                    $em->flush();

                                    array_push($existe_alertas ,$alerta->getId());
                                }
                                
                                // borra las que no esten dentro del array que envio el usuario                  
                                if(count($existe_alertas) > 0){ 
                                    $existe_alertas = implode(",", $existe_alertas);
                                    $sql = "DELETE FROM matriz_indicadores_etab_alertas  WHERE id not in($existe_alertas) and matriz_indicador_etab_id = ".$etab->getId();
                                    $statement = $em->getConnection()->prepare($sql);
                                    $statement->execute();   
                                }
                                
                            }
                        }
                        
                        // borra las que no esten dentro del array que envio el usuario                  
                        if(count($existe_etab) > 0){ 
                            $existe_etab = implode(",", $existe_etab);
                            $sql = "DELETE FROM matriz_indicadores_etab  WHERE id not in($existe_etab) and id_desempeno = ".$desempeno->getId();
                            $statement = $em->getConnection()->prepare($sql);
                            $statement->execute();   
                        }
                        
                    }
                }
                
                // borra las que no esten dentro del array que envio el usuario                  
                if(count($existe_desemepno) > 0){ 
                    $existe_desemepno = implode(",", $existe_desemepno);
                    $sql = "DELETE FROM matriz_indicadores_desempeno  WHERE id not in($existe_desemepno) and id_matriz = ".$data->getId();
                    $statement = $em->getConnection()->prepare($sql);
                    $statement->execute();   
                }
                
            }
            if(property_exists($datos, "usuarios")){
                $sql = "DELETE FROM matriz_indicadores_usuario  WHERE id_matriz = ".$data->getId();
                $statement = $em->getConnection()->prepare($sql);
                $statement->execute();     
                foreach($datos->usuarios as $clave => $valor){                     
                    
                    $usuario = new MatrizIndicadoresUsuario();

                    $usuario->setMatriz($em->getRepository(MatrizSeguimientoMatriz::class)->find($data->getId()));
                    $usuario->setUsuario($em->getRepository(User::class)->find($valor));

                    $em->persist($usuario); 

                    $em->flush();
                }                               
            }
                              
            // devolver el mensaje en caso de que la contraseña no sea correcta
            $response = [
                'status' => 200,
                'messages' => "Ok",
                'data' => $data,
            ];
        }catch(\Exception $e){
            $response = [
                'status' => 500,
                'messages' => $e->getMessage(),
                'data' => [],
            ];    
        }
        return $response; 
    }

    /**
     * @Route("/MatrizConfiguracion/{id}", name="MatrizConfiguracion_show", methods={"GET"})
     */
    public function show($id, Request $request, AlmacenamientoProxy $almacenamiento){
        // iniciar para la repuesta en formato json
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);   
        try{
            // validar que ningun dato est vacio
            if(isset($id) &&  $id != ''){
                // iniciar el manager de doctrine
                $em = $this->getDoctrine()->getManager();
                // Consultar que el modulo exista
                $data = $em->getRepository(MatrizSeguimientoMatriz::class)->find($id);
                // si existe el modulo
                if($data){
                    $datos = array(
                        "id" => $data->getId(),
                        "nombre" => $data->getNombre(),
                        "descripcion" => $data->getDescripcion()
                    );
                    // ejecutar el contenido de la memoria
                    $em->flush();

                    // indicadores de desempeño
                    $connection = $em->getConnection();
                    $statement = $connection->prepare("SELECT * FROM matriz_indicadores_desempeno WHERE  id_matriz = '".$data->getId()."'");
                    $statement->execute();
                    $desempenos = $statement->fetchAll();
                   
                    $indicadores_desempeno = [];
                    foreach($desempenos as $desempeno){
                        $desempeno["abierto"] = false;

                        // relaciones asignadas
                        $statement = $connection->prepare("SELECT * FROM matriz_indicadores_relacion WHERE  id_desempeno = '".$desempeno["id"]."'");
                        $statement->execute();
                        $relaciones = $statement->fetchAll();
                    
                        $relaciones_asignados = [];
                        foreach($relaciones as $relacion){
                             // relaciones alertas
                            $statement = $connection->prepare("SELECT * FROM matriz_indicadores_relacion_alertas WHERE  matriz_indicador_relacion_id = '".$relacion["id"]."'");
                            $statement->execute();
                            $alertas = $statement->fetchAll();
                        
                            $relaciones_alertas = [];
                            foreach($alertas as $alerta){
                                $alerta["color"] = (array) json_decode($alerta["color"]);
                                array_push($relaciones_alertas, $alerta);
                            }
                            $relacion["alertas"] = $relaciones_alertas;
                            array_push($relaciones_asignados, $relacion);
                        }

                        // etab asignadas
                        $statement = $connection->prepare("SELECT * FROM matriz_indicadores_etab WHERE  id_desempeno = '".$desempeno["id"]."'");
                        $statement->execute();
                        $etab = $statement->fetchAll();
                    
                        $etab_asignados = [];
                        foreach($etab as $key => $item){
                            $filtros = json_decode($item["filtros"]); 
                            $fichaTec = $em->getRepository(FichaTecnica::class)->find($item["id_ficha_tecnica"]);
                            if($fichaTec){
                                $filtrar = []; $filtrar2 = [];
                                foreach ($filtros->filtros as $f) {  
                                    $f = (object) $f;
                                    $filtrar[$f->codigo] = $f->valor;
                                    array_push($filtrar2, array(
                                        "codigo" => $f->codigo,
                                        "valor" => $f->valor
                                    ));
                                } 
                                $otros_filtros = (array) $filtros->otros_filtros;
                                if(!array_key_exists("elementos", $otros_filtros)){
                                    $otros_filtros["elementos"] = [];
                                }
                                $dimension = trim($filtros->dimensiones[$filtros->dimension]);
                                $almacenamiento->crearIndicador($fichaTec, $dimension, $filtrar);
                                $data_indicador = $almacenamiento->calcularIndicador($fichaTec, $dimension, $filtrar, false, $otros_filtros, false);
                                
                                $indicador = array(
                                    "id" => $item["id"],
                                    "filtros" => $filtrar2,
                                    "error" => "",
                                    "indicador" => $item["id_ficha_tecnica"],
                                    "nombre" => $fichaTec->getNombre(),
                                    "es_favorito" => false,
                                    "dimensiones" => $filtros->dimensiones,
                                    "dimension" => $filtros->dimension,
                                    "posicion" => $key + 1,
                                    "index" => $key,
                                    "otros_filtros" => $otros_filtros,
                                    "data" => $data_indicador,
                                    "informacion" => $this->dimensionIndicador($fichaTec)
                                );

                                // etab alertas
                                $statement = $connection->prepare("SELECT * FROM matriz_indicadores_etab_alertas WHERE  matriz_indicador_etab_id = '".$item["id"]."'");
                                $statement->execute();
                                $alertas = $statement->fetchAll();
                            
                                $etab_alertas = [];
                                foreach($alertas as $alerta){
                                    $alerta["color"] = (array) json_decode($alerta["color"]);
                                    array_push($etab_alertas, $alerta);
                                }
                                $indicador["alertas"] = $etab_alertas;
                                array_push($etab_asignados, $indicador);
                            }
                        }

                        $desempeno["indicador_etab"] = $etab_asignados;
                        $desempeno["indicador_relacion"] = $relaciones_asignados;
                        array_push($indicadores_desempeno, $desempeno);
                    }

                    // usuarios asignados                    
                    $statement = $connection->prepare("SELECT id_usuario FROM matriz_indicadores_usuario WHERE  id_matriz = '".$data->getId()."'");
                    $statement->execute();
                    $usuarios = $statement->fetchAll();
                   
                    $usuarios_asignados = [];
                    foreach($usuarios as $user){
                        array_push($usuarios_asignados, $user["id_usuario"]);
                    }

                    $datos["indicadores_desempeno"] = $indicadores_desempeno;
                    $datos["usuarios"] = $usuarios_asignados;
                    $connection->close();
                    // devolver el mensaje en caso de que la contraseña no sea correcta
                    $response = [
                        'status' => 200,
                        'messages' => "Ok",
                        'data' => $datos,
                    ];                    
                } else{ // devolver el mensaje en caso de que el modulo no sea correcto
                    $response = [
                        'status' => 404,
                        'messages' => "Not Found",
                        'data' => [],
                    ];
                }
            }else{
                $response = [
                    'status' => 400,
                    'messages' => "Bad Request",
                    'data' => [],
                ];            
            }
            // devolver la respuesta en json 
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
     * @Route("/MatrizConfiguracion/{id}", name="MatrizConfiguracion_destroy", methods={"DELETE"})
     */
    public function destroy($id, Request $request){
        // iniciar para la repuesta en formato json
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        try{
            // validar que ningun dato est vacio
            if(isset($id) &&  $id != ''){
                // iniciar el manager de doctrine
                $em = $this->getDoctrine()->getManager();
                // Consultar que el modulo exista
                $data = $em->getRepository(MatrizSeguimientoMatriz::class)->find($id);
                // si existe el modulo
                if($data){
                    $em->remove($data);
                    // ejecutar el contenido de la memoria
                    $em->flush();
                     // devolver el mensaje en caso de que la contraseña no sea correcta
                    $response = [
                        'status' => 200,
                        'messages' => "Ok",
                        'data' => $data,
                    ];                    
                } else{ // devolver el mensaje en caso de que el modulo no sea correcto
                    $response = [
                        'status' => 404,
                        'messages' => "Not Found",
                        'data' => [],
                    ];
                }
            }else{
                $response = [
                    'status' => 400,
                    'messages' => "Bad Request",
                    'data' => [],
                ];            
            }
            // devolver la respuesta en json 
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
            $es_favorito = false;
            
            

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
}