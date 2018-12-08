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
use App\Message\SmsGuardarOrigenDatos;


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

    private function enviarMsjFinal ($idOrigen, $ahora, $idConexion) {
        //Después de enviados todos los registros para guardar, mandar mensaje para borrar los antiguos
        $msg_guardar = array('id_origen_dato' => $idOrigen,
            'method' => 'DELETE',
            'ultima_lectura' => $ahora,
            'id_conexion' =>$idConexion,
            'numMsj' => $this->numMsj++
        );

        //$this->container->get('old_sound_rabbit_mq.guardar_registro_producer')
        //->publish(json_encode($msg_guardar));
        $this->bus->dispatch(new SmsGuardarOrigenDatos($msg_guardar));
    }

    private function enviarMsjInicio ($idOrigen) {
        $msg_init = array('id_origen_dato' => $idOrigen,
            'method' => 'BEGIN',
            'r' => microtime(true),
            'numMsj' => $this->numMsj++
        );
        //$this->container->get('old_sound_rabbit_mq.guardar_registro_producer')
        //->publish(json_encode($msg_init));
        $this->bus->dispatch(new SmsGuardarOrigenDatos($msg_init));
    }

    private function enviarDatos($idOrigen, $datos, $campos_sig, $ultima_lectura, $idConexion, $lim_inf = '' , $lim_sup = '') {

        $origenDato = $this->em->find(OrigenDatos::class, $idOrigen);

        if ( count($datos) > 0 ) {
            $datos_a_enviar = $this->almacenamiento->prepararDatosEnvio($idOrigen, $campos_sig, $datos, $ultima_lectura, $idConexion);

            $msg_guardar = array('id_origen_dato' => $idOrigen,
                'method' => 'PUT',
                'datos' => $datos_a_enviar,
                'ultima_lectura' => $ultima_lectura,
                'id_conexion' => $idConexion,
                'r' => microtime(true),
                'numMsj' => $this->numMsj++,
                'lim_inf' => $lim_inf,
                'lim_sup' => $lim_sup
            );

            $this->bus->dispatch(new SmsGuardarOrigenDatos($msg_guardar));

            if ( $origenDato->getCampoLecturaIncremental() != null ){
                $ultimo = array_pop($datos_a_enviar);
                try {
                    $fecha = \DateTime::createFromFormat( $origenDato->getFormatoFechaCorte(), $ultimo[$origenDato->getCampoLecturaIncremental()->getSignificado()->getCodigo()] );
                    $origenDato->setFechaCorte($fecha);
                    $this->em->persist($origenDato);
                    $this->em->flush();
                } catch (\Exception $e) {}
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

        // Es lectura desde bases de datos
        if ($origenDato->getSentenciaSql() != '') {


            //Verificar si el origen de datos tiene un campo para lectura incremental
            $campoLecturaIncremental = $origenDato->getCampoLecturaIncremental();
            $condicion_carga_incremental = "";
            $ultimaLecturaIncremental = null;
            $esLecturaIncremental = ( $campoLecturaIncremental == null or $origenDato->getFechaCorte() == null) ? false: true;
            $orden = " ";
            $lim_inf= '';
            $lim_sup =  '';
            if ( $esLecturaIncremental ){
                //tomar la fecha de la última actualización del origen
                $campoIncremental = $campoLecturaIncremental->getCodigo();
                $significadoCampoIncremental = $campoIncremental->getSignificado()->getCodigo();

                //Calcular los límites
                $ventana_inf = ($origenDato->getVentanaLimiteInferior() == null) ? 0 : $origenDato->getVentanaLimiteInferior();
                $ventana_sup = ($origenDato->getVentanaLimiteSuperior() == null) ? 0 : $origenDato->getVentanaLimiteSuperior();

                $fechaIni = $origenDato->getFechaCorte();
                $fechaFin = new \DateTime();

                if ( $significadoCampoIncremental == 'fecha' or $significadoCampoIncremental == 'date') {
                    $lim_inf = $fechaIni->sub(new \DateInterval('P' . $ventana_inf . 'D'))->format($origenDato->getFormatoFechaCorte());
                    $lim_sup = $fechaFin->sub(new \DateInterval('P' . $ventana_sup . 'D'))->format($origenDato->getFormatoFechaCorte());
                } else {
                    // Se está utilizando el campo año para la carga incremental
                    $lim_inf = $fechaIni->format('Y') - $ventana_inf ;
                    $lim_sup = $fechaFin->format('Y') - $ventana_sup;
                }

                $condicion_carga_incremental = " AND $campoIncremental >= '$lim_inf'
                                                                 AND $campoIncremental <= '$lim_sup' ";

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


                $sql = $origenDato->getSentenciaSql();

                foreach ($origenDato->getConexiones() as $cnx) {
                    $leidos = $tamanio + 1;
                    $i = 0;

                    $this->logger->info('******************* Conexión :'.$cnx );

                    $lect = 1;
                    $datos = true;
                    $this->enviarMsjInicio($idOrigenDatos);

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
                            $this->enviarDatos($idOrigenDatos, $datos, $campos_sig, $ahora, $cnx->getId(), $lim_inf, $lim_sup);
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
                        $msg_ = array('id_origen_dato' => $idOrigenDatos,
                            'method' => 'ERROR_LECTURA',
                            'id_conexion' =>$cnx->getId(),
                            'numMsj' => $this->numMsj++,
                            'r' => microtime(true),
                        );

                        $this->bus->dispatch(new SmsGuardarOrigenDatos($msg_));

                        $origenDato->setErrorCarga(true);
                        $origenDato->setMensajeErrorCarga(' Conexion: ' . $cnx->getId() . ' Error: ' . $datos);
                        $this->em->flush();
                    }
                    else{
                        $this->enviarMsjFinal($idOrigenDatos, $ahora, $cnx->getId());
                    }
                }

                $tfc = new \DateTime();
                $this->logger->info('============================= FIN DE CARGA de origen de datos: '. $origenDato . ' <BR> Finalizada en : '. $tfc->format('H:i:s.v'));

                $d = $tfc->diff($tic) ;
                $dm = abs ( $tfc->format('v') - $tic->format('v') );
                $this->logger->info('DURACION: '. $d->i . ':' . $d->s . '.' . $dm );
                $origenDato->setUltimaActualizacion($fecha);
                $this->em->flush();
            } catch (\Exception $e) {
                echo 'CODC 1' . $e->getMessage();
                $this->logger->error($e->getFile() . '( '.$e->getLine().') '.$e->getMessage());
            }

        } else {

            $datos = $this->em->getRepository(OrigenDatos::class)
                ->getDatos(null, null, $this->params->get('app.upload_directory'), $origenDato->getArchivoNombre(), $this->phpspreadsheet);
            $this->enviarMsjInicio($idOrigenDatos);

            $this->enviarDatos($idOrigenDatos, $datos, $campos_sig, $ahora, 0);

            $this->enviarMsjFinal($idOrigenDatos, $ahora, 0);
        }


    }

}