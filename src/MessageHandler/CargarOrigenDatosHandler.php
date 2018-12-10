<?php

namespace App\MessageHandler;


use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use App\AlmacenamientoDatos\AlmacenamientoProxy;
use App\Message\SmsCargarOrigenDatos;
use App\Entity\OrigenDatos;
//use App\Message\SmsGuardarOrigenDatos;


class CargarOrigenDatosHandler implements MessageHandlerInterface
{

    private $em;
    private $bus;
    private $numMsj = 0;
    private $almacenamiento;
    private $params;
    private $logger;

    public function __construct(EntityManagerInterface $em, MessageBusInterface $bus, AlmacenamientoProxy $almacenamiento, ParameterBagInterface $params, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->bus = $bus;
        $this->almacenamiento = $almacenamiento;
        $this->params = $params;
        $this->logger = $logger;
    }

    public function __invoke( SmsCargarOrigenDatos $message ) {

        $idOrigen = $message->getIdOrigenDatos();

        $origenDato = $this->em->find(OrigenDatos::class, $idOrigen);

        if ( $origenDato != null ) {
            $this->procesarCarga( $idOrigen );
        }

    }

    private function enviarMsjFinal ($idOrigen, $ahora, $idConexion, $cargaId, $lim_inf = '', $lim_sup = '') {
        //Después de enviados todos los registros para guardar, mandar mensaje para borrar los antiguos
        /*$msg_guardar = array('id_origen_dato' => $idOrigen,
            'method' => 'DELETE',
            'ultima_lectura' => $ahora,
            'id_conexion' =>$idConexion,
            'numMsj' => $this->numMsj++,
            'carga_id' => $cargaId,
            'lim_inf' => $lim_inf,
            'lim_sup' => $lim_sup
        );

        $this->bus->dispatch(new SmsGuardarOrigenDatos($msg_guardar));*/

        $origenDato = $this->em->find(OrigenDatos::class, $idOrigen);

        $areaCosteo = $origenDato->getAreaCosteo();
        $tabla = 'origenes.fila_origen_dato_' . $idOrigen;
        $cnx = $this->em->getConnection();

        //verificar si la tabla existe
        $this->almacenamiento->inicializarTabla($idOrigen, $idConexion);

        if ($areaCosteo['area_costeo'] == 'rrhh') {
            //Solo agregar los datos nuevos
            $sql = " INSERT INTO $tabla 
                                SELECT *  FROM $tabla" . "_tmp 
                                WHERE id_origen_dato='$idOrigen'
                                    AND datos->>'nit' 
                                        NOT IN 
                                        (SELECT datos->>'nit' FROM $tabla); 
                                DROP TABLE IF EXISTS " . $tabla . '_tmp';
            $cnx->exec($sql);

        } elseif ($areaCosteo['area_costeo'] == 'ga_af') {
            //Solo agregar los datos nuevos
            $sql = " INSERT INTO $tabla 
                                SELECT *  FROM $tabla" . "_tmp
                                WHERE id_origen_dato='$idOrigen'
                                    AND datos->>'codigo_af' 
                                        NOT IN 
                                        (SELECT datos->>'codigo_af' FROM $tabla); 
                            DROP TABLE IF EXISTS " . $tabla . '_tmp; ';
            $cnx->exec($sql);
        } else {

            if ( $origenDato->getCampoLecturaIncremental() != null and $origenDato->getValorCorte() != null
                and $lim_inf != '' AND  $lim_sup != '') {
                $this->almacenamiento->guardarDatosIncremental($idConexion, $idOrigen, $cargaId, $lim_inf, $lim_sup );
            } else {
                //Pasar todos los datos de la tabla auxiliar a la tabla destino final
                $this->almacenamiento->guardarDatos( $idConexion, $idOrigen, $cargaId );
            }
        }

        $inicio = new \DateTime($ahora);
        $fin = new \DateTime("now");
        $diffInSeconds = $fin->getTimestamp() - $inicio->getTimestamp();

        $origenDato->setTiempoSegundosUltimaCarga($diffInSeconds);
        $origenDato->setCargaFinalizada(true);
        //$this->em->getConnection()->exec($sql);

        //Poner la fecha de última lectura para todas las fichas que tienen este origen de datos
        $ahora2 = new \DateTime();
        $origenDato->setUltimaActualizacion($ahora2);

        foreach ($origenDato->getVariables() as $var) {
            foreach ($var->getIndicadores() as $ind) {
                $ind->setUltimaLectura($ahora2);
                $this->em->persist($ind);
            }
        }

        $this->em->flush();

        $this->logger->info('Carga finalizada de origen ' . $idOrigen . ' Para la conexión ' . $idConexion );

        //Recalcular la tabla del indicador
        //Recuperar las variables en las que está presente el origen de datos
        $origenDatos = $this->em->find(OrigenDatos::class, $idOrigen);
        foreach ($origenDatos->getVariables() as $var) {
            foreach ($var->getIndicadores() as $ind) {
                //$this->bus->dispatch( new SmsCargarIndicadorEnTablero( $ind->getId() ) );
            }
        }
    }


    private function enviarDatos($idOrigen, $datos, $campos_sig, $ultima_lectura, $idConexion, $cargaId, $lim_inf = '', $lim_sup='') {

        $origenDato = $this->em->find(OrigenDatos::class, $idOrigen);

        $bus = $this->bus;

        $send = function ($datosEnv, $indice) use ($idOrigen, $idConexion,  $cargaId, $origenDato ){
            /*$msg_guardar = array('id_origen_dato' => $idOrigen,
                'method' => 'PUT',
                'datos' => $datosEnv,
                'ultima_lectura' => $ultima_lectura,
                'id_conexion' => $idConexion,
                'r' => microtime(true),
                'numMsj' => $indice + 1,
                'carga_id' => $cargaId,
                'lim_inf' => $lim_inf,
                'lim_sup' => $lim_sup
            );
            $bus->dispatch(new SmsGuardarOrigenDatos($msg_guardar));*/

            $ti = microtime(true);

            try {

                $this->almacenamiento->insertarEnAuxiliar($idOrigen, $idConexion, $datosEnv, $cargaId);

            } catch (\Exception $e) {
                $error = ' Conexion : ' . $idConexion . ' Error: ' . $e->getMessage();
                $this->logger->error($e->getFile(). '('.$e->getLine().') ' .$error);

                $origenDato->setErrorCarga(true);
                $origenDato->setMensajeErrorCarga($error);
                $this->em->flush();

            }

            $tf = microtime(true);
            $d = $tf - $ti;
            $this->logger->info('--> DURACIÓN(s): ' . number_format($d/1000000, 10)) ;
        };

        if ( count($datos) > 0 ) {
            $datos_a_enviar = $this->almacenamiento->prepararDatosEnvio($idOrigen, $campos_sig, $datos, $ultima_lectura, $idConexion);

            //Enviaré a guardar en pedazos de 3000
            $partes = array_chunk($datos_a_enviar, 3000);
            array_walk( $partes, $send);

            if ( $origenDato->getCampoLecturaIncremental() != null ) {

                $valorCorte = array_pop($datos_a_enviar);

                $origenDato->setValorCorte( $valorCorte[$origenDato->getCampoLecturaIncremental()->getSignificado()->getCodigo()]);
                $this->em->persist($origenDato);

                $this->em->flush();
            }


        }
    }

    private function procesarCarga ( $idOrigenDatos ){

        $origenDato = $this->em->find(OrigenDatos::class, $idOrigenDatos);
        $fecha = new \DateTime("now");
        $ahora = $fecha->format('Y-m-d H:i:s');

        // Recuperar el nombre y significado de los campos del origen de datos
        $campos_sig = array();
        $campos = $origenDato->getCampos();
        foreach ($campos as $campo) {
            $campos_sig[$campo->getNombre()] = $campo->getSignificado()->getCodigo();
        }

        $cargaId = uniqid();

        // Es lectura desde bases de datos
        if ($origenDato->getSentenciaSql() != '') {


            //Verificar si el origen de datos tiene un campo para lectura incremental
            $campoLecturaIncremental = $origenDato->getCampoLecturaIncremental();
            $condicion_carga_incremental = "";
            $ultimaLecturaIncremental = null;
            $esLecturaIncremental = ( $campoLecturaIncremental == null or $origenDato->getValorCorte() == null) ? false: true;

            $orden = " ";
            $lim_inf= '';
            $lim_sup =  '';
            if ( $esLecturaIncremental ){
                //tomar la fecha de la última actualización del origen
                $campoIncremental = $campoLecturaIncremental->getNombre();
                $significadoCampoIncremental = $campoLecturaIncremental->getSignificado()->getCodigo();

                //Calcular los límites
                $ventana_inf = ($origenDato->getVentanaLimiteInferior() == null) ? 0 : $origenDato->getVentanaLimiteInferior();
                $ventana_sup = ($origenDato->getVentanaLimiteSuperior() == null) ? 0 : $origenDato->getVentanaLimiteSuperior();

                $valorCorte = $origenDato->getValorCorte();
                $fechaFin = new \DateTime();

                if ( $significadoCampoIncremental == 'fecha' or $significadoCampoIncremental == 'date') {
                    $fechaIni = \Datetime::createFromFormat($origenDato->getFormatoValorCorte(), $valorCorte );
                    $lim_inf = $fechaIni->sub(new \DateInterval('P' . $ventana_inf . 'D'))->format($origenDato->getFormatoFechaCorte());
                    $lim_sup = $fechaFin->sub(new \DateInterval('P' . $ventana_sup . 'D'))->format($origenDato->getFormatoFechaCorte());

                    $condicion_carga_incremental = " AND $campoIncremental > '$lim_inf'
                                                                 AND $campoIncremental <= '$lim_sup' ";
                } else {
                    // Se está utilizando el campo año para la carga incremental
                    $lim_inf = $valorCorte - $ventana_inf ;
                    $lim_sup = $fechaFin->format('Y') - $ventana_sup;

                    $condicion_carga_incremental = " AND $campoIncremental > $lim_inf
                                                                 AND $campoIncremental <= $lim_sup ";

                }

                $orden = " ORDER BY $campoIncremental ";
            }

            $origenDato->setErrorCarga(false);
            $origenDato->setMensajeErrorCarga('');
            $this->em->flush();

            $tic = new \DateTime();
            $this->logger->info('============================== INICIO CARGA de origen de datos: '. $origenDato );

            try {
                //Leeré los datos en grupos de 100,000
                $tamanio = 100000;

                //Identificador de la carga

                $sql = $origenDato->getSentenciaSql();

                foreach ($origenDato->getConexiones() as $cnx) {
                    $leidos = $tamanio + 1;
                    $i = 0;

                    $this->logger->info('******************* Conexión :'.$cnx );

                    $lect = 1;
                    $datos = true;
                    $this->enviarMsjInicio($idOrigenDatos, $cargaId);

                    while ($leidos >= $tamanio and $datos != false) {
                        $errorEnLectura = false;
                        if ($cnx->getIdMotor()->getCodigo() == 'oci8') {
                            $sql_aux = ($esLecturaIncremental) ?
                                "SELECT * FROM ( $sql )  sqlOriginal 
                                    WHERE  1 = 1
                                        $condicion_carga_incremental
                                        AND ROWNUM >= " . $i * $tamanio . ' AND ROWNUM < ' . ($tamanio * ($i + 1)) .
                                $orden :
                                'SELECT * FROM (' . $sql . ')  sqlOriginal ' .
                                'WHERE ROWNUM >= ' . $i * $tamanio . ' AND ROWNUM < ' . ($tamanio * ($i + 1));
                        } elseif ($cnx->getIdMotor()->getCodigo() == 'pdo_dblib') {
                            $sql_aux = ( $esLecturaIncremental) ?
                                "SELECT * FROM ( $sql )  sqlOriginal 
                                    WHERE 1 = 1
                                    $condicion_carga_incremental
                                    $orden " : $sql;
                        } else {
                            $sql_aux = ($esLecturaIncremental) ?
                                "SELECT * FROM ( $sql) sqlOriginal 
                                        WHERE 1 = 1
                                        $condicion_carga_incremental
                                        $orden
                                        LIMIT " . $tamanio . ' OFFSET ' . $i * $tamanio :
                                $sql . ' LIMIT ' . $tamanio . ' OFFSET ' . $i * $tamanio;
                            ;
                        }

                        $ti = new \DateTime();
                        $this->logger->info('-------------------> Lectura de datos '. $lect . '## INICIO: '. $ti->format('H:i:s.v') );

                        $datos = $this->em->getRepository(OrigenDatos::class)->getDatos($sql_aux, $cnx);

                        $tf = new \DateTime();
                        $d = $tf->diff($ti) ;
                        $dm = abs ( $tf->format('v') - $ti->format('v') );

                        $this->logger->info(' ## FIN: '. $tf->format('H:i:s.v') . '  ## DURACION: '. $d->i . ':' . $d->s. '.'. $dm);

                        if ($datos === false){
                            $leidos = 1;
                            $errorEnLectura = true;
                            $this->logger->warning('## SIN REGISTROS  ---> Origen: '.$idOrigenDatos);
                        } else {
                            $this->enviarDatos($idOrigenDatos, $datos, $campos_sig, $ahora, $cnx->getId(), $cargaId, $lim_inf, $lim_sup);
                            if ($cnx->getIdMotor()->getCodigo() == 'pdo_dblib')
                                $leidos = 1;
                            else
                                $leidos = count($datos);
                            $i++;
                            $this->logger->info(' ## Cantidad de registros :' . $leidos) ;
                        }
                        $lect++;

                    }

                    if ( $errorEnLectura ){
                        /*$msg_ = array('id_origen_dato' => $idOrigenDatos,
                            'method' => 'ERROR_LECTURA',
                            'id_conexion' =>$cnx->getId(),
                            'numMsj' => $this->numMsj++,
                            'r' => microtime(true),
                            'cargaId' => $cargaId
                        );

                        $this->bus->dispatch(new SmsGuardarOrigenDatos($msg_));*/
                        $this->almacenamiento->borrarTablaAuxiliar( $idOrigenDatos, $cnx->getId() );

                        $origenDato->setErrorCarga(true);
                        $origenDato->setMensajeErrorCarga(' Conexion: ' . $cnx->getId() . ' Error: ' . $datos);
                        $this->em->flush();
                    }
                    else{
                        $this->enviarMsjFinal($idOrigenDatos, $ahora, $cnx->getId(), $cargaId, $lim_inf, $lim_sup);
                    }
                }

                $tfc = new \DateTime();
                $this->logger->info('============================= FIN DE CARGA de origen de datos: '. $origenDato . '  Finalizada en : '. $tfc->format('H:i:s.v'));

                $d = $tfc->diff($tic) ;
                $dm = abs ( $tfc->format('v') - $tic->format('v') );
                $this->logger->info('DURACION: '. $d->i . ':' . $d->s . '.' . $dm );
                $origenDato->setUltimaActualizacion($fecha);
                $this->em->flush();
            } catch (\Exception $e) {
                $this->logger->error($e->getFile() . '( '.$e->getLine().') '.$e->getMessage());
            }

        } else {

            $datos = $this->em->getRepository(OrigenDatos::class)
                ->getDatos(null, null, $this->params->get('app.upload_directory'), $origenDato->getArchivoNombre(), $this->phpspreadsheet);
            $this->enviarMsjInicio( $idOrigenDatos );

            $this->enviarDatos($idOrigenDatos, $datos, $campos_sig, $ahora, 0, $cargaId);

            $this->enviarMsjFinal($idOrigenDatos, $ahora, 0, $cargaId);
        }


    }

    private function enviarMsjInicio ( $idOrigen ) {
        /*$msg_init = array('id_origen_dato' => $idOrigen,
            'method' => 'BEGIN',
            'r' => microtime(true),
            'numMsj' => $this->numMsj++,
            'carga_id' => $cargaId
        );

        $this->bus->dispatch(new SmsGuardarOrigenDatos($msg_init));*/

        $origenDato = $this->em->find(OrigenDatos::class, $idOrigen);

        $cnx = $this->em->getConnection();
        $areaCosteo = $origenDato->getAreaCosteo();
        $tabla = 'origenes.fila_origen_dato_' . $idOrigen;

        // Iniciar borrando los datos que pudieran existir en la tabla auxiliar
        if (($areaCosteo['area_costeo'] != '')) {
            $sql = ' DROP TABLE IF EXISTS costos.fila_origen_dato_' . $areaCosteo['area_costeo'] . ';
                            SELECT * INTO ' . $tabla . "_tmp FROM fila_origen_dato_v2 LIMIT 0;                
                   ";
            $cnx->exec($sql);
        } else {
            $this->almacenamiento->inicializarTablaAuxliar( $idOrigen );
        }

        $origenDato->setCargaFinalizada(false);
        $this->em->flush();
    }

}