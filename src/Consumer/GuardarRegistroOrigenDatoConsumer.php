<?php

namespace App\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\OrigenDatos;
use App\AlmacenamientoDatos\AlmacenamientoProxy;

class GuardarRegistroOrigenDatoConsumer implements ConsumerInterface {

    protected $em;
    protected $almacenamiento;

    public function __construct(EntityManagerInterface $em, AlmacenamientoProxy $almacenamiento) {
        $this->em = $em;
        $this->almacenamiento = $almacenamiento;
    }

    public function execute(AMQPMessage $mensaje) {
        $msg = json_decode($mensaje->body, true);
        echo '  Msj: '. $msg['id_origen_dato']. '/'.  (array_key_exists('numMsj', $msg) ? $msg['numMsj'] : '--') . '  ';

        $origenDato = $this->em->find(OrigenDatos::class, $msg['id_origen_dato']);

        //Verificar si tiene código de costeo
        $areaCosteo = $origenDato->getAreaCosteo();

        $tabla = 'origenes.fila_origen_dato_' . $msg['id_origen_dato'];
        $cnx = $this->em->getConnection();

        $idConexion = ( array_key_exists('id_conexion', $msg) ) ? $msg['id_conexion'] : 'null' ;

        if ($msg['method'] == 'BEGIN') {
            // Iniciar borrando los datos que pudieran existir en la tabla auxiliar
            if ( ($areaCosteo['area_costeo'] != '') ){
                $sql = ' DROP TABLE IF EXISTS costos.fila_origen_dato_'.$areaCosteo['area_costeo'].';
                        SELECT * INTO '.$tabla."_tmp FROM fila_origen_dato_v2 LIMIT 0;                
               ";
                $cnx->exec($sql);
            } else {
                $this->almacenamiento->inicializarTablaAuxliar($msg['id_origen_dato']);
            }

            $origenDato->setCargaFinalizada(false);
            $this->em->flush();
            return true;

        } elseif ($msg['method'] == 'PUT') {

            echo '(inicio: '.microtime(true);

            try {

                $this->almacenamiento->insertarEnAuxiliar($msg['id_origen_dato'], $msg['id_conexion'], $msg['datos']);

            } catch (\Exception $e) {
                $error = ' Conexion : ' .$idConexion . ' Error: ' . $e->getMessage() ;
                echo $error;
                $origenDato->setErrorCarga(true);
                $origenDato->setMensajeErrorCarga($error);
                $this->em->flush();
                return true;
            }
            echo ' - fin: '.microtime(true) . ') ****';
            return true;
        } elseif ($msg['method'] == 'ERROR_LECTURA') {
            $this->almacenamiento->borrarTablaAuxiliar($msg['id_origen_dato']);
        } elseif ($msg['method'] == 'DELETE') {
            //verificar si la tabla existe
            $this->almacenamiento->inicializarTabla('origenes.fila_origen_dato_' . $msg['id_origen_dato']);

            if ($areaCosteo['area_costeo'] == 'rrhh') {
                //Solo agregar los datos nuevos
                $sql = " INSERT INTO $tabla 
                            SELECT *  FROM $tabla"."_tmp 
                            WHERE id_origen_dato='$msg[id_origen_dato]'
                                AND datos->>'nit' 
                                    NOT IN 
                                    (SELECT datos->>'nit' FROM $tabla); 
                            DROP TABLE IF EXISTS ".$tabla.'_tmp' ;
                $cnx->exec($sql);

            } elseif ($areaCosteo['area_costeo'] == 'ga_af') {
                //Solo agregar los datos nuevos
                $sql = " INSERT INTO $tabla 
                            SELECT *  FROM $tabla"."_tmp
                            WHERE id_origen_dato='$msg[id_origen_dato]'
                                AND datos->>'codigo_af' 
                                    NOT IN 
                                    (SELECT datos->>'codigo_af' FROM $tabla); 
                        DROP TABLE IF EXISTS ".$tabla.'_tmp; ';
                $cnx->exec($sql);
            } else {

                if ($msg['es_lectura_incremental']) {
                    $this->almacenamiento->guardarDatosIncremental($idConexion, $msg['id_origen_dato'], $msg['campo_lectura_incremental'], $msg['lim_inf'], $msg['lim_sup']);
                } else {
                    //Pasar todos los datos de la tabla auxiliar a la tabla destino final
                    $this->almacenamiento->guardarDatos($idConexion, $msg['id_origen_dato']);

                }
            }

            $inicio = new \DateTime($msg['ultima_lectura']);
            $fin = new \DateTime("now");
            $diffInSeconds = $fin->getTimestamp() - $inicio->getTimestamp();

            $origenDato->setTiempoSegundosUltimaCarga($diffInSeconds);
            $origenDato->setCargaFinalizada(true);
            //$this->em->getConnection()->exec($sql);

            //Poner la fecha de última lectura para todas las fichas que tienen este origen de datos
            $ahora = new \DateTime();
            $origenDato->setUltimaActualizacion($ahora);

            if ($origenDato != false ){
                foreach ($origenDato->getVariables() as $var) {
                    foreach ($var->getIndicadores() as $ind) {
                        $ind->setUltimaLectura($ahora);
                    }
                }
            }

            $this->em->flush();

            echo '
            Carga finalizada de origen ' . $msg['id_origen_dato'] . ' Para la conexión ' . $idConexion . '  

            ';
            //$this->em->getConnection()->commit();

            /* Mover esto a otro lugar más adecuado, aquí hace que la carga de los indicadores tarde mucho
              //Recalcular la tabla del indicador
              //Recuperar las variables en las que está presente el origen de datos
              $origenDatos = $this->em->find('IndicadoresBundle:OrigenDatos', $msg['id_origen_dato']);
              foreach ($origenDatos->getVariables() as $var) {
              foreach ($var->getIndicadores() as $ind) {
              $fichaTec = $this->em->find('IndicadoresBundle:FichaTecnica', $ind->getId());
              $fichaRepository = $this->em->getRepository('IndicadoresBundle:FichaTecnica');
              $fichaRepository->crearCamposIndicador($fichaTec);
              if (!$fichaTec->getEsAcumulado())
              $fichaRepository->crearIndicador($fichaTec);
              }
              } */

            return true;
        }
    }

}