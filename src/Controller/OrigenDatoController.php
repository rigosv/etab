<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL as DBAL;
use Symfony\Component\Translation\TranslatorInterface;

use App\Entity\OrigenDatos;
use App\Entity\Campo;
use App\Entity\Conexion;
use App\Entity\MotorBd;
use App\Entity\TipoCampo;
use App\Entity\Diccionario;
use App\Service\Util;
use App\Message\SmsCargarOrigenDatos;
use App\Entity\SignificadoCampo;



class OrigenDatoController extends Controller
{
    private $driver;

    /**
     * @Route("/conexion/probar", name="origen_dato_conexion_probar", options={"expose"=true})
     */
    public function probarConexionAction(Request $request, TranslatorInterface $translator)
    {
        try {
            $conn = $this->getConexionGenerica('base_datos', null, $request);

            $conn->connect();
            $mensaje = '<span style="color: green">' . $translator->trans('conexion_success') . '</span>';
        } catch (\Exception $e) {
            $mensaje = '<span style="color: red">' . $translator->trans('conexion_error') . ': ' . $e->getMessage() . '</span>';
        } catch (DBAL\Exception\ConnectionException  $e) {
            $mensaje = '<span style="color: red">' . $translator->trans('conexion_error') . ': ' . $e->getMessage() . '</span>';
        }

        return new Response($mensaje);
    }

    /**
     * @Route("/sentencia/probar", name="origen_dato_conexion_probar_sentencia", options={"expose"=true})
     */
    public function probarSentenciaAction(Request $request, TranslatorInterface $translator)
    {
        $resultado = array('estado' => 'error', 'mensaje' => '', 'datos' => array());
        $sql = $request->get('sql');
        $conexiones = explode(',', trim($request->get('conexiones_todas'), '-'));

        // Verificar que no tenga UPDATE o DELETE
        $patron = '/\bUPDATE\b|\bDELETE\b|\bINSERT\b|\bCREATE\b|\bDROP\b/i';
        $conexion = '';
        if (preg_match($patron, $sql) == FALSE) {
            try {
                foreach ($conexiones as $cnx) {
                    $datos = array();
                    $cnxObj = $this->getDoctrine()->getManager()->find(Conexion::class, $cnx);
                    $conn = $this->getConexionGenerica('consulta_sql', $cnxObj, $request);
                    $conexion = $cnxObj->getNombreConexion();

                    if ($this->driver == 'sqlsrv') {
                        $datos = $cnx->executeQuery('SELECT TOP 1 (' . $sql . ') cons')->fetchAll();
                        dump($datos); exit;
                        $sql_ = 'SELECT TOP 20 * FROM (' . $sql . ') cons';
                    } else {
                        $sql_ = ' SELECT * FROM (' . $sql . ' ) A LIMIT 20';
                    }
                    $query = $conn->query($sql_);

                    while ($row = $query->fetch()) {
                        $datos[] = $row;
                    }


                    $resultado['estado'] = 'ok';
                    $resultado['mensaje'] = '<span style="color: green">' . $translator->trans('sentencia_success') . '</span>';
                    $resultado['datos'] = array_merge($resultado['datos'], $datos);
                }
            } catch (\PDOException $e) {
                $resultado['mensaje'] = '<span style="color: red">' . $conexion . ' ' . $translator->trans('sentencia_error') . ': ' . $e->getMessage() . '</span>';
            } catch (DBAL\DBALException $e) {
                $resultado['mensaje'] = '<span style="color: red">' . $conexion . ' ' . $translator->trans('sentencia_error') . ': ' . $e->getMessage() . '</span>';
            } catch (\Exception $e) {
                $resultado['mensaje'] = '<span style="color: red">' . $conexion . ' ' . $translator->trans('sentencia_error') . ': ' . $e->getMessage() . '</span>';
            }
        } else {
            $resultado['mensaje'] = $translator->trans('solo_select');
        }

        //verificar que no hayan problemas con codificación de caracteres
        $datos_aux = array();
        foreach ($resultado['datos'] as $fila) {
            $nueva_fila = array();
            foreach ($fila as $k => $v)
                $nueva_fila[$k] = trim(mb_check_encoding($v, 'UTF-8') ? $v : utf8_encode($v));
            $datos_aux[] = $nueva_fila;
        }
        $resultado['datos'] = $datos_aux;

        return new Response(json_encode($resultado));
    }

    /**
     * Crear una conexión para realizar pruebas
     * @param type $objeto_prueba, puede ser 'base_datos' o 'consulta_sql'
     */
    public function getConexionGenerica($objeto_prueba, $conexion = null, Request $request)
    {
        $req = $request;
        $em = $this->getDoctrine()->getManager();

        if ($objeto_prueba == 'base_datos') {
            $motor = $em->find(MotorBd::class, $req->get('idMotor'));
            $datos = array('dbname' => $req->get('nombreBaseDatos'),
                'user' => $req->get('usuario'),
                'password' => $req->get('clavefirst'),
                'host' => $req->get('ip'),
                'driver' => $motor->getCodigo(),
                'port' => $req->get('puerto')
            );
        } elseif ($objeto_prueba == 'consulta_sql') {

            $datos = array('dbname' => $conexion->getNombreBaseDatos(),
                'user' => $conexion->getUsuario(),
                'password' => $conexion->getClave(),
                'host' => $conexion->getIp(),
                'driver' => $conexion->getIdMotor()->getCodigo(),
                'port' => $conexion->getPuerto()
            );
        }

        $this->driver = $datos['driver'];

        // Construir el Conector genérico
        $config = new DBAL\Configuration();

        $connectionParams = array(
            'dbname' => $datos['dbname'],
            'user' => $datos['user'],
            'password' => $datos['password'],
            'host' => $datos['host'],
            'driver' => $datos['driver']
        );
        if ($datos['port'] != '' and $datos['driver'] != 'pdo_sqlite')
            $connectionParams['port'] = $datos['port'];

        $conn = DBAL\DriverManager::getConnection($connectionParams, $config);


        return $conn;
    }

    /**
     * @Route("/origen_dato/{id}/leer", name="origen_dato_leer", options={"expose"=true})
     */
    public function leerOrigenAction(OrigenDatos $origenDato, Request $request, Util $util)
    {
        $resultado = array('estado' => 'ok',
            'mensaje' => '',
            'tipo_origen' => '',
            'es_catalogo' => '',
            'nombre_campos' => array(),
            'datos' => array());
        $recargar = ($request->get('recargar')=='false') ? false : true;

        $em = $this->getDoctrine()->getManager();

        $resultado['es_catalogo'] = ($origenDato->getEsCatalogo()) ? true : false;

        $sql = "SELECT tp
                    FROM App\Entity\TipoCampo tp
                    ORDER BY tp.descripcion";
        $resultado['tipos_datos'] = $em->createQuery($sql)->getArrayResult();

        $sql = "SELECT dic
                    FROM App\Entity\Diccionario dic
                    ORDER BY dic.descripcion";
        $resultado['diccionarios'] = $em->createQuery($sql)->getArrayResult();

        $sql = "SELECT sv
                    FROM App\Entity\SignificadoCampo sv
                    WHERE sv.usoEnCatalogo = :uso_en_catalogo
                    ORDER BY sv.descripcion";
        $resultado['significados'] = $em->createQuery($sql)
            ->setParameter('uso_en_catalogo', $resultado['es_catalogo'] ? 'true' : 'false')
            ->getArrayResult();

        //recuperar los campos ya existentes en el origen de datos
        $campos_existentes = $em->getRepository(Campo::class)->findBy(array('origenDato' => $origenDato));

        $campos = array();
        foreach ($campos_existentes as $campo)
            $campos[$campo->getNombre()]['id'] = $campo->getId();

        $resultado['campos'] = $campos;
        //Por defecto poner tipo entero
        $tipoCampo = $em->getRepository(TipoCampo::class)->findOneByCodigo('varchar(255)');
        if (count($campos_existentes) == 0 or $recargar == true) {
            if ($origenDato->getSentenciaSql() != '') {
                $resultado['tipo_origen'] = 'sql';
                $sentenciaSQL = $origenDato->getSentenciaSql();
                $conexiones = $origenDato->getConexiones();

                if (count($conexiones) == 0) {
                    $resultado['mensaje'] = $this->get('translator')->trans('sentencia_error') . ': ' . $this->get('translator')->trans('_no_conexion_configurada_');
                    $resultado['estado'] = 'error';
                } else {
                    $jj = 0;
                    $resultado['estado'] = 'error';
                    while ($jj < count($conexiones) and $resultado['estado'] != 'ok')
                    {
                        $resultado = $this->getDatosMuestra($conexiones[$jj++], $sentenciaSQL, $request);
                    }

                }
            } else {
                $phpspreadsheet = $this->get('phpspreadsheet');
                $resultado['tipo_origen'] = 'archivo';

                $extension = explode( '.', $origenDato->getArchivoNombre());
                $ext = array_pop($extension);

                $tipo = ucwords($ext);

                $reader = $phpspreadsheet->createReader($tipo);
                $reader->setReadDataOnly(true);

                try {
                    $hoja = $reader->load( $this->getParameter('app.upload_directory').'/'.$origenDato->getArchivoNombre() )->getSheet(0);

                    $datos = $hoja->toArray($nullValue = null, $calculateFormulas = true, $formatData = false, $returnCellRef = false);
                    $resultado['nombre_campos'] = array_values(array_shift($datos));

                    // Buscar por columnas que tengan null en el título
                    $primer_null = array_search(null, $resultado['nombre_campos']);

                    if ($primer_null == false)
                        foreach ($datos as $fila)
                            $resultado['datos'][] = $fila;
                    else {
                        $resultado['nombre_campos'] = array_slice($resultado['nombre_campos'], 0, $primer_null, true);
                        foreach ($datos as $fila) {
                            $resultado['datos'][] = array_slice($fila, 0, $primer_null, true);
                        }
                    }
                    $resultado['estado'] = 'ok';

                    // Poner el nombre de la columna como indice del arreglo
                    $aux = array();
                    foreach ($resultado['datos'] as $fila)
                        $aux[] = array_combine($resultado['nombre_campos'], $fila);
                    $resultado['datos'] = $aux;
                } catch (\Exception $e) {
                    $resultado['estado'] = 'error';
                    $resultado['mensaje'] = $e->getMessage();
                }
            }
            // Guardar los campos
            if ($resultado['estado'] == 'ok') {
                $nombres_id = array();
                $campo = array();

                foreach ($resultado['nombre_campos'] as $k => $nombre) {
                    // si existe no guardarlo
                    $nombre_campo = $util->slug($nombre);
                    if (!array_key_exists($nombre_campo, $campos)) {
                        $campo[$k] = new Campo();
                        $campo[$k]->setNombre($nombre_campo);
                        $campo[$k]->setOrigenDato($origenDato);
                        $campo[$k]->setTipoCampo($tipoCampo);
                        $em->persist($campo[$k]);
                        $nombres_id[$campo[$k]->getId()] = $nombre_campo;
                    } else
                        $nombres_id[$campos[$nombre_campo]['id']] = $nombre_campo;
                }
                //Borrar algún campo que ya no se use
                foreach ($campos_existentes as $campo) {
                    if (!in_array($campo->getNombre(), $nombres_id))
                        $em->remove($campo);
                }
                try {
                    $em->flush();
                } catch (\Exception $e) {
                    $resultado = array('estado' => 'error', 'mensaje' => '<div class="alert alert-error"> ' . $this->get('translator')->trans('camio_no_realizado') . '</div>');
                }
                $resultado['nombre_campos'] = $nombres_id;
            }

            $campos_existentes = $em->getRepository(Campo::class)->findBy(array('origenDato' => $origenDato));
        } else {
            foreach ($campos_existentes as $campo) {
                $nombre_campos[$campo->getId()] = $campo->getNombre();
            }
            $resultado['nombre_campos'] = $nombre_campos;
        }
        $campos = array();
        foreach ($campos_existentes as $campo) {
            $campos[$campo->getNombre()]['id'] = $campo->getId();
            $campos[$campo->getNombre()]['significado'] = ($campo->getSignificado()) ? $campo->getSignificado()->getId() : null;
            $campos[$campo->getNombre()]['significado_codigo'] = ($campo->getSignificado()) ? $campo->getSignificado()->getCodigo() : null;
            $campos[$campo->getNombre()]['diccionario'] = ($campo->getDiccionario()) ? $campo->getDiccionario()->getId() : null;
            $campos[$campo->getNombre()]['tipo'] = ($campo->getTipoCampo()) ? $campo->getTipoCampo()->getId() : null;
        }
        $resultado['campos'] = $campos;

        //Cambiar la estructura
        $aux = array();
        foreach ($resultado['nombre_campos'] as $n)
            $aux[$n] = '';
        foreach (array_slice($resultado['datos'], 0, 10) as $fila)
            foreach ($fila as $k => $v)
                $aux[$util->slug($k)] .= trim(mb_check_encoding($v, 'UTF-8') ? $v : utf8_encode($v)) . ', ';
        $resultado['datos'] = $aux;

        return new Response(json_encode($resultado));
    }

    /**
     * @Route("/configurar/campo", name="configurar_campo", options={"expose"=true})
     */
    public function configurarCampoAction(Request $request, Util $util)
    {
        $resultado = array('estado' => 'success', 'mensaje' => '');

        $em = $this->getDoctrine()->getManager();
        $req = $request;
        list($tipo_cambio, $id) = explode('__', $req->get('control'));
        $valor = $req->get('valor');
        $campo = $em->find(Campo::class, $id);
        $valido = true;
        if ($tipo_cambio == 'tipo_campo') {
            $tipo_campo = $em->find(TipoCampo::class, $valor);
            if (strlen($req->get('datos_prueba'))) {
                $datos_prueba = explode(', ', $req->get('datos_prueba'));


                foreach ($datos_prueba as $dato) {
                    $valido = $util->validar($dato, $tipo_campo->getCodigo());
                    if (!$valido)
                        break;
                }
            }
            $mensaje = $campo->getNombre() . ': ' . $this->get('translator')->trans('tipo_campo_cambiado_a') . ' ' . $tipo_campo->getDescripcion();
            $campo->setTipoCampo($tipo_campo);
        } elseif ($tipo_cambio == 'significado_variable') {
            $significado_variable = $em->find(SignificadoCampo::class, $valor);
            $mensaje = $campo->getNombre() . ': ' . $this->get('translator')->trans('significado_campo_cambiado_a') . ' ' . $significado_variable->getDescripcion();
            $campo->setSignificado($significado_variable);
        } else {
            $diccionario = $em->find(Diccionario::class, $valor);
            $mensaje = $campo->getNombre() . ': ' . $this->get('translator')->trans('_diccionario_aplicado_') . ' ' . $diccionario->getDescripcion();
            $campo->setDiccionario($diccionario);
        }

        if ($valido) {
            $resultado['mensaje'] = $mensaje;
        } else {
            $resultado = array('estado' => 'error', 'mensaje' => $this->get('translator')->trans('_tipo_no_corresponde_con_datos_'));
        }
        try {
            $em->flush();
        } catch (\Exception $e) {
            $resultado = array('estado' => 'error', 'mensaje' => $this->get('translator')->trans('cambio_no_realizado'));
        }

        return new Response(json_encode($resultado));
    }

    /**
     * @Route("/origen_dato/get_campos/{id}", name="origen_dato_get_campos", options={"expose"=true})
     */
    public function getCamposAction(OrigenDatos $origen)
    {
        $resp = '<h6>' . $this->get('translator')->trans('_campos_utilizables_en_campos_calculados_') . '</h6>
                <UL class="campos_disponibles">';
        if ($origen->getEsFusionado() or $origen->getEsPivote()) {
            $campos = explode(',', str_replace(array(' ', "'"), '', $origen->getCamposFusionados()));
            foreach ($campos as $campo)
                $resp .= '<LI><A href="javascript:funcion()">{' . $campo . '}</A></LI>';
        } else {
            $campos = $origen->getCampos();
            foreach ($campos as $campo) {
                if ($campo->getSignificado())
                    $resp .= '<LI><A href="javascript:funcion()">{' . $campo->getSignificado()->getCodigo() . '}</A></LI>';
            }
        }

        return new Response($resp . '</UL>');
    }

    public function getDatosMuestra($conexion, $sentenciaSQL, $request) {
        $conn = $this->getConexionGenerica('consulta_sql', $conexion, $request);
        $resultado = array();
        $resultado['estado'] = 'error';
        $resultado['mensaje'] = $this->get('translator')->trans('_error_conexion_');
        if ($conexion != false){
            try {

                if ($this->driver == 'sqlsrv') {
                    $sql_ = 'SELECT TOP 20 * FROM (' . $sentenciaSQL . ') cons';
                } else {
                    $sql_ = ' SELECT * FROM (' . $sentenciaSQL . ' ) A LIMIT 20';
                }
                $query = $conn->query($sql_);

                $datos = array();
                while ( $row = $query->fetch() ) {
                    $datos[] = $row;
                }
                $resultado['datos'] = $datos;


                if (count($resultado['datos']) > 0){
                    $resultado['nombre_campos'] = array_keys($resultado['datos'][0]);

                    $resultado['estado'] = 'ok';
                    $resultado['mensaje'] = '<span style="color: green">' . $this->get('translator')->trans('sentencia_success');
                } else {
                    $resultado['estado'] = 'error';
                    $resultado['mensaje'] = $this->get('translator')->trans('_no_datos_');
                }
            } catch (\PDOException $e) {
                $resultado['mensaje'] = $this->get('translator')->trans('sentencia_error') . ' 1: ' . $e->getMessage();
            } catch (DBAL\DBALException $e) {
                $resultado['mensaje'] = $this->get('translator')->trans('sentencia_error') . ' 2: ' . $e->getMessage();
            } catch (\Exception $e) {
                $resultado['mensaje'] = $this->get('translator')->trans('sentencia_error') . ' 3: ' . $e->getMessage();
            }
        }

        return $resultado;
    }

    /**
     * @Route("/origen_dato/cargar/{id}", name="origen_dato_cargar", options={"expose"=true})
     */
    public function cargarOrigen(OrigenDatos $origen, TranslatorInterface $translator, MessageBusInterface $bus) {
        $em = $this->getDoctrine()->getManager();

        $configurado = $em->getRepository(OrigenDatos::class)->estaConfigurado($origen);

        $resp = ['estado' => 'success', 'mensaje' => $translator->trans('_se_ha_iniciado_la_carga_del_origen_') . $origen->getNombre() ];
        if  ( $configurado ) {
            $bus->dispatch(new SmsCargarOrigenDatos($origen->getId()));
        } else {
            $resp = ['estado' => 'danger', 'mensaje' => $origen->getNombre() . ': ' . $translator->trans('origen_no_configurado') ];
        }

        return new JsonResponse( $resp );

    }

    /**
     * @Route("/origen_datos/guardar/archivo", name="guardar_archivo", options={"expose"=true})
     */
    public function guardarArchivo(Request $request){

        $em = $this->getDoctrine()->getManager();
        $dir = $this->getParameter('app.upload_directory');

        $fileSystem = new Filesystem();

        // Si no existe el directorio crearlo
        if (! $fileSystem->exists($dir)) {
            try {
                $fileSystem->mkdir($dir);
            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while creating your directory at ".$exception->getPath();
            }
        }

        $files = $request->files->all();
        $fileA = array_shift($files);
        $file = array_shift($fileA);
        $originalName = $file->getClientOriginalName();
        $idOrigen = $request->get('idOrigen');

        if ( $idOrigen != null ){
            $origen = $em->find(OrigenDatos::class, $idOrigen);
            $archivoAnt = $origen->getArchivoNombre();
        }


        try {
            //Si existía un archivo para ese origen de datos borrarlo
            if ( $archivoAnt != '' ) {
                $fileSystem->remove($dir.'/'.$archivoAnt);
            }

            $file->move(
                $dir,
                $originalName
            );

            $origen->setArchivoNombre($originalName);
            $em->persist($origen);
            $em->flush();
        } catch (FileException $e) {
            return new JsonResponse(['success'=>0, 'error' => $e->getMessage()]);
        }
        return new JsonResponse(['success'=>1]);
    }

}
