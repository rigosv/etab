<?php

namespace App\MessageHandler;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

use App\AlmacenamientoDatos\AlmacenamientoProxy;
use App\Message\SmsCargarOrigenDatos;
use App\Entity\OrigenDatos;
use App\Message\SmsGuardarOrigenDatos;
use Onurb\Bundle\ExcelBundle\Factory\ExcelFactory;

class CargarOrigenDatosHandler implements MessageHandlerInterface
{

    private $em;
    private $bus;
    private $numMsj = 0;
    private $almacenamiento;

    public function __construct(EntityManagerInterface $em, MessageBusInterface $bus, AlmacenamientoProxy $almacenamiento)
    {
        $this->em = $em;
        $this->bus = $bus;
        $this->almacenamiento = $almacenamiento;
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

    private function enviarDatos($idOrigen, $datos, $campos_sig, $ultima_lectura, $idConexion) {
        $datos_a_enviar = array();
        $i = 0;
        $ii = 0;
        $grpMsj = 1;

        if ( count($datos) > 0 ) {
            $datos_a_enviar = $this->almacenamiento->prepararDatosEnvio($idOrigen, $campos_sig, $datos, $ultima_lectura, $idConexion);

            $msg_guardar = array('id_origen_dato' => $idOrigen,
                'method' => 'PUT',
                'datos' => $datos_a_enviar,
                'ultima_lectura' => $ultima_lectura,
                'id_conexion' => $idConexion,
                'r' => microtime(true),
                'numMsj' => $this->numMsj++
            );

            $this->bus->dispatch(new SmsGuardarOrigenDatos($msg_guardar));
            //$msg = json_encode($msg_guardar);

            /*try {
                $this->container->get('old_sound_rabbit_mq.guardar_registro_producer')
                    ->publish($msg);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }*/


        }
        echo ' 
            ';
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
            $esLecturaIncremental = ($campoLecturaIncremental == null) ? false: true;
            $orden = " ";
            $lim_inf= '';
            $lim_sup =  '';
            if ($esLecturaIncremental){
                //tomar la fecha de la última actualización del origen
                $campoLecturaIncremental = $campoLecturaIncremental->getSignificado()->getCodigo();

                //Calcular los límites
                $ventana_inf = ($origenDato->getVentanaLimiteInferior() == null) ? 0 : $origenDato->getVentanaLimiteInferior();
                $ventana_sup = ($origenDato->getVentanaLimiteSuperior() == null) ? 0 : $origenDato->getVentanaLimiteSuperior();

                if ($campoLecturaIncremental == 'fecha'){
                    $fechaIni = $fecha;
                    $fechaFin = $fecha;

                    $lim_inf = $fechaIni->sub(new \DateInterval('P'.$ventana_inf.'D'))->format('Y-m-d H:i:s');
                    $lim_sup = $fechaFin->sub(new \DateInterval('P'.$ventana_sup.'D'))->format('Y-m-d H:i:s');
                } else {
                    // Se está utilizando el campo año para la carga incremental
                    $lim_inf = $fecha->format('Y') - $ventana_inf ;
                    $lim_sup = $fecha->format('Y') - $ventana_sup;
                }
                $condicion_carga_incremental = " AND $campoLecturaIncremental >= '$lim_inf'
                                                                 AND $campoLecturaIncremental <= '$lim_sup' ";

                $orden = " ORDER BY $campoLecturaIncremental ";
            }

            $origenDato->setErrorCarga(false);
            $origenDato->setMensajeErrorCarga('');
            $this->em->flush();

            $tic = new \DateTime();
            echo '
============================== INICIO CARGA de origen de datos: '. $origenDato .'
Empezando en: '. $tic->format('H:i:s.v');

            try {
                //Leeré los datos en grupos de 10,000
                $tamanio = 10;


                $sql = $origenDato->getSentenciaSql();

                foreach ($origenDato->getConexiones() as $cnx) {
                    $leidos = $tamanio + 1;
                    $i = 0;

                    echo '
    
    ******************* Conexión :'.$cnx ;

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
                        echo '
        
        -------------------> Lectura de datos '. $lect . '
            ## INICIO: '. $ti->format('H:i:s.v');

                        $datos = $this->em->getRepository(OrigenDatos::class)->getDatos($sql_aux, $cnx);

                        $tf = new \DateTime();
                        $d = $tf->diff($ti) ;
                        $dm = abs ( $tf->format('v') - $ti->format('v') );

                        echo '  ## FIN: '. $tf->format('H:i:s.v') . '  ## DURACION: '. $d->i . ':' . $d->s. '.'. $dm;

                        if ($datos === false){
                            $leidos = 1;
                            $errorEnLectura = true;
                            echo '  ## SIN REGISTROS  ---> Origen: '.$idOrigenDatos.'
                
                ' ;
                        } else {
                            $this->enviarDatos($idOrigenDatos, $datos, $campos_sig, $ahora, $cnx->getId());
                            if ($cnx->getIdMotor()->getCodigo() == 'pdo_dblib')
                                $leidos = 1;
                            else
                                $leidos = count($datos);
                            $i++;
                            echo ' ## Cantidad de registros :' . $leidos.'
                
                ' ;
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

                        //$this->container->get('old_sound_rabbit_mq.guardar_registro_producer')
                        //   ->publish(json_encode($msg_));
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
                echo '
============================= FIN DE CARGA de origen de datos: '. $origenDato . ' <BR> Finalizada en : '. $tfc->format('H:i:s.v');

                $d = $tfc->diff($tic) ;
                $dm = abs ( $tfc->format('v') - $tic->format('v') );
                echo '<BR/>DURACION: '. $d->i . ':' . $d->s . '.' . $dm;
                $origenDato->setUltimaActualizacion($fecha);
                $this->em->flush();
            } catch (\Exception $e) {
                echo 'CODC 1' . $e->getMessage();
            }

        } else {

            $datos = $this->em->getRepository(OrigenDatos::class)->getDatos(null, null, $origenDato->getFile()->getRealPath(), $origenDato->getFile()->getMimeType(), $this->phpspreadsheet);
            $this->enviarMsjInicio($idOrigenDatos);

            $this->enviarDatos($idOrigenDatos, $datos, $campos_sig, $ahora, 0);

            $this->enviarMsjFinal($idOrigenDatos, $ahora, 0);
        }


    }

}