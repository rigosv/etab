<?php

namespace App\MessageHandler;


use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

use App\Entity\OrigenDatos;
use App\Message\SmsGuardarOrigenDatos;
use App\AlmacenamientoDatos\AlmacenamientoProxy;
use App\Message\SmsCargarIndicadorEnTablero;

class GuardarOrigenDatosHandler implements MessageHandlerInterface
{
    private $em;
    private $bus;
    private $almacenamiento;
    private $origenDato;
    private $msg;
    private $idConexion;
    private $logger;

    public function __construct(EntityManagerInterface $em, MessageBusInterface $bus, AlmacenamientoProxy $almacenamiento, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->bus = $bus;
        $this->almacenamiento =  $almacenamiento;
        $this->logger = $logger;
    }

    public function __invoke(SmsGuardarOrigenDatos $message)
    {
        //dump($message);

        $this->msg = $message->getDatos();
        $this->logger->info(' GUARDANDO DATOS <br/> Msj: ' . $this->msg['id_origen_dato'] . '/' . (array_key_exists('numMsj', $this->msg) ? $this->msg['numMsj'] : '--') );

        $this->origenDato = $this->em->find(OrigenDatos::class, $this->msg['id_origen_dato']);

        if ( $this->origenDato != null ) {
            //Verificar si tiene código de costeo
            $areaCosteo = $this->origenDato->getAreaCosteo();

            $tabla = 'origenes.fila_origen_dato_' . $this->msg['id_origen_dato'];

            $this->msg['id_conexion'] = (array_key_exists('id_conexion', $this->msg)) ? $this->msg['id_conexion'] : 0;

            if ($this->msg['method'] == 'BEGIN') {
                $this->mensajeBegin();
            } elseif ($this->msg['method'] == 'PUT') {
                $this->mensajePut();
            } elseif ($this->msg['method'] == 'ERROR_LECTURA') {
                $this->almacenamiento->borrarTablaAuxiliar($this->msg['id_origen_dato'], $this->msg['id_conexion']);
            } elseif ($this->msg['method'] == 'DELETE') {
                $this->mensajeDelete();
            }
        }

        $this->logger->info('FIN GUARDAR');
    }

    private function mensajePut(){
        $ti = microtime(true);

        try {

            $this->almacenamiento->insertarEnAuxiliar($this->msg['id_origen_dato'], $this->msg['id_conexion'], $this->msg['datos']);

        } catch (\Exception $e) {
            $error = ' Conexion : ' . $this->idConexion . ' Error: ' . $e->getMessage();
            $this->logger->error($e->getFile(). '('.$e->getLine().') ' .$error);
            $this->origenDato->setErrorCarga(true);
            $this->origenDato->setMensajeErrorCarga($error);
            $this->em->flush();

        }

        $tf = microtime(true);
        $d = $tf - $ti;
        $this->logger->info('--> DURACIÓN(s): ' . number_format($d/1000000, 10)) ;

    }

    private function mensajeBegin(){
        $cnx = $this->em->getConnection();
        $areaCosteo = $this->origenDato->getAreaCosteo();
        $tabla = 'origenes.fila_origen_dato_' . $this->msg['id_origen_dato'];

        // Iniciar borrando los datos que pudieran existir en la tabla auxiliar
        if (($areaCosteo['area_costeo'] != '')) {
            $sql = ' DROP TABLE IF EXISTS costos.fila_origen_dato_' . $areaCosteo['area_costeo'] . ';
                            SELECT * INTO ' . $tabla . "_tmp FROM fila_origen_dato_v2 LIMIT 0;                
                   ";
            $cnx->exec($sql);
        } else {
            $this->almacenamiento->inicializarTablaAuxliar($this->msg['id_origen_dato'], $this->msg['id_conexion']);
        }

        $this->origenDato->setCargaFinalizada(false);
        $this->em->flush();
    }

    private function mensajeDelete(){
        $areaCosteo = $this->origenDato->getAreaCosteo();
        $tabla = 'origenes.fila_origen_dato_' . $this->msg['id_origen_dato'];
        $cnx = $this->em->getConnection();

        //verificar si la tabla existe
        $this->almacenamiento->inicializarTabla($this->msg['id_origen_dato'], $this->msg['id_conexion']);

        if ($areaCosteo['area_costeo'] == 'rrhh') {
            //Solo agregar los datos nuevos
            $sql = " INSERT INTO $tabla 
                                SELECT *  FROM $tabla" . "_tmp 
                                WHERE id_origen_dato='$this->msg[id_origen_dato]'
                                    AND datos->>'nit' 
                                        NOT IN 
                                        (SELECT datos->>'nit' FROM $tabla); 
                                DROP TABLE IF EXISTS " . $tabla . '_tmp';
            $cnx->exec($sql);

        } elseif ($areaCosteo['area_costeo'] == 'ga_af') {
            //Solo agregar los datos nuevos
            $sql = " INSERT INTO $tabla 
                                SELECT *  FROM $tabla" . "_tmp
                                WHERE id_origen_dato='$this->msg[id_origen_dato]'
                                    AND datos->>'codigo_af' 
                                        NOT IN 
                                        (SELECT datos->>'codigo_af' FROM $tabla); 
                            DROP TABLE IF EXISTS " . $tabla . '_tmp; ';
            $cnx->exec($sql);
        } else {

            if ( $this->origenDato->getCampoLecturaIncremental() != null ) {
                $campoLecturaInc = $this->origenDato->getCampoLecturaIncremental();
                $limInf = $this->origenDato->getVentanaLimiteInferior();
                $limSup =  $this->origenDato->getVentanaLimiteSuperior();
                $this->almacenamiento->guardarDatosIncremental($this->msg['id_conexion'], $this->msg['id_origen_dato'],
                    $campoLecturaInc, $limInf, $limSup);
            } else {
                //Pasar todos los datos de la tabla auxiliar a la tabla destino final
                $this->almacenamiento->guardarDatos($this->msg['id_conexion'], $this->msg['id_origen_dato']);
            }
        }

        $inicio = new \DateTime($this->msg['ultima_lectura']);
        $fin = new \DateTime("now");
        $diffInSeconds = $fin->getTimestamp() - $inicio->getTimestamp();

        $this->origenDato->setTiempoSegundosUltimaCarga($diffInSeconds);
        $this->origenDato->setCargaFinalizada(true);
        //$this->em->getConnection()->exec($sql);

        //Poner la fecha de última lectura para todas las fichas que tienen este origen de datos
        $ahora = new \DateTime();
        $this->origenDato->setUltimaActualizacion($ahora);

        if ($this->origenDato != false) {
            foreach ($this->origenDato->getVariables() as $var) {
                foreach ($var->getIndicadores() as $ind) {
                    $ind->setUltimaLectura($ahora);
                }
            }
        }

        $this->em->flush();

        $this->logger->info('Carga finalizada de origen ' . $this->msg['id_origen_dato'] . ' Para la conexión ' . $this->idConexion );

        //Recalcular la tabla del indicador
        //Recuperar las variables en las que está presente el origen de datos
        $origenDatos = $this->em->find(OrigenDatos::class, $this->msg['id_origen_dato']);
        foreach ($origenDatos->getVariables() as $var) {
            foreach ($var->getIndicadores() as $ind) {
                //$this->bus->dispatch( new SmsCargarIndicadorEnTablero( $ind->getId() ) );
            }
        }
    }

}