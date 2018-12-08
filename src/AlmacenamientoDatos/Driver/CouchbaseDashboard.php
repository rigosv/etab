<?php

namespace App\AlmacenamientoDatos\Driver;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use App\Entity\FichaTecnica;
use App\AlmacenamientoDatos\DashboardInterface;
use App\Entity\SignificadoCampo;

class CouchbaseDashboard implements DashboardInterface
{
    private $em;
    private $doc = 'origen_';
    private $docIndicador = 'indicador_';
    private $bucketName = 'etab_origenes';
    private $bucket;
    private $bucketNameIndicador = 'etab_indicadores';
    private $bucketIndicador ;

    public function __construct(EntityManager $em, ParameterBagInterface $params)
    {
        $this->em = $em;

        $authenticator = new \Couchbase\PasswordAuthenticator();
        $authenticator->username($params->get('couchbase_user'))->password($params->get('couchbase_password'));

        // Connect to Couchbase Server
        $cluster = new \CouchbaseCluster($params->get('couchbase_url'));

        // Authenticate, then open bucket
        $cluster->authenticate($authenticator);
        $this->bucket = $cluster->openBucket($this->bucketName);
        $this->bucketIndicador = $cluster->openBucket($this->bucketNameIndicador);

        $this->bucket->operationTimeout = 240 * 1000; //240 segundos

    }


    public function crearIndicador(FichaTecnica $fichaTec, $dimension=null, $filtros=null) {
        //No es necesaria esta función en couchbase

    }

    private function getFuenteDatos( FichaTecnica $fichaTec, $dimension=null, $filtros=null ) {
        $formula = strtoupper($fichaTec->getFormula());

        //Los campos de la ficha técnica determinan que campos se utilizarán de los orígenes de datos
        $campos = str_replace(' ', '', $fichaTec->getCamposIndicador());

        $camposVar = ($dimension == null ) ? 'v.' . str_replace(',', ', v.', $campos) : 'v.' . $dimension;
        $camposInd = ($dimension == null ) ? 'i.' . str_replace(',', ', i.', $campos) : 'i.' . $dimension;

        //Recuperar los datos de los orígenes asociados a cada variable del indicador
        $stmVar = '';
        $varStmFin = '';
        foreach ($fichaTec->getVariables() as $variable) {
            $origen = $variable->getOrigenDatos();
            $nombreVar = strtolower( $variable->getIniciales() );
            $origenesIds = [];

            if ( $origen->getEsFusionado() ) {
                foreach ($origen->getFusiones() as $of) {
                    $origenesIds[] = $of->getId();
                }
            } else {
                $origenesIds[] = $origen->getId();
            }

            //Obtener el operador de la variable
            $oper_ = explode('{'.$variable->getIniciales().'}', str_replace(' ', '', $formula));
            $tieneOperadores = preg_match('/([A-Z]+)\($/', $oper_[0], $coincidencias, PREG_OFFSET_CAPTURE);
            $oper = ($tieneOperadores) ? $coincidencias[1][0] : 'SUM';

            $varStmFin .= "$oper($nombreVar) AS  $nombreVar, ";

            $stmVar .= " 
                    SELECT $camposVar,  $oper(TONUMBER(v.calculo)) AS  $nombreVar 
                    FROM `$this->bucketName` t UNNEST t.datos v WHERE t.id_origen_datos IN [".implode(',',$origenesIds)."]
                    GROUP BY $camposVar
                    UNION";

        }
        $varStmFin = trim($varStmFin, ', ');
        $stmVar = trim($stmVar,'UNION');


        $stmFin = "
                    SELECT $camposInd, $varStmFin
                        FROM (
                            $stmVar
                        ) AS i
                    GROUP BY $camposInd
                    ";
        return $stmFin;
    }

    public function calcularIndicador($fichaTecnica, $dimension, $filtros, $verSql){

        $formula = str_replace(' ', '',strtolower($fichaTecnica->getFormula()));

        //Recuperar las variables
        $variables = array();
        preg_match_all('/\{[a-z0-9\_]{1,}\}/', strtolower($formula), $variables, PREG_SET_ORDER);

        $denominador = explode('/', $fichaTecnica->getFormula());
        $evitar_div_0 = '';
        $variables_d = array();
        if ( count($denominador) > 1 ) {
            preg_match('/\{.{1,}\}/', $denominador[1], $variables_d);
            if (count($variables_d) > 0)
                $var_d = strtolower(str_replace(array('{', '}'), array('(vv.', ')'), array_shift($variables_d)));
            $evitar_div_0 = 'AND ' . $var_d . ' > 0';
        }

        // Formar la cadena con las variables para ponerlas en la consulta
        $variables_query = '';
        foreach ($variables as $var) {
            $oper_ = explode($var[0], $formula);
            $tieneOperadores = preg_match('/([A-Za-z]+)\($/', $oper_[0], $coincidencias, PREG_OFFSET_CAPTURE);

            $oper = ($tieneOperadores) ? $coincidencias[1][0] : 'SUM';

            $v = str_replace(array('{', '}'), array('', ''), $var[0]);

            $formula = str_replace($var[0], (($oper=='SUM') ? $oper : '').str_replace(array('{','}'), array('(vv.', ')'),$var[0]), $formula);

            $variables_query .= " $oper(vv.$v) as $v, ";
        }
        $variables_query = trim($variables_query, ', ');

        $nombre_indicador = $fichaTecnica->getId();
        //$doc_indicador = $this->docIndicador . $nombre_indicador;

        $filtros = '';
        if ($filtros != null) {
            foreach ($filtros as $campo => $valor) {
                $filtros .= " AND vv." . $campo . " = '$valor' ";
            }
        }

        $fuente = $this->getFuenteDatos($fichaTecnica, $dimension, $filtros);

        //$sql = "SELECT v.$dimension AS category,  $variables_query, ROUND(($formula),2) AS measure
        //   FROM `".$this->bucketNameIndicador."` A USE KEYS '$doc_indicador' UNNEST A v";
        $decimales = ( $fichaTecnica->getCantidadDecimales() == null ) ? 2 : $fichaTecnica->getCantidadDecimales();
        $sql = 'SELECT vv.'. $dimension .' AS category,  '. $variables_query .', ROUND(( '. $formula .' ), '. $decimales .') AS measure
            FROM ( '. $fuente . ') AS vv';

        $sql .= ' WHERE 1 = 1 ' . $evitar_div_0 . ' ' . $filtros;

        $sql .= '
            GROUP BY vv. '. $dimension;
        $sql .=  ' HAVING (( ' . $formula . ' )) > 0 ';
        $sql .= ' ORDER BY vv.'.$dimension ;

        try {
            if ($verSql == true)
                return $sql;
            else {
                $query = \Couchbase\N1qlQuery::fromString($sql);
                $result = $this->bucket->query($query);
                return $result->rows;
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getAnalisisDescriptivo($sql){
        //return $this->fichaRepository->getAnalisisDescriptivo($sql);
    }

    public function totalRegistrosIndicador(FichaTecnica $fichaTec){
        $datos = $this->getDatosIndicador($fichaTec);

        return count($datos);
    }

    public function getDatosIndicador(FichaTecnica $fichaTecnica, $offset = 0 , $limit = 100000000000000) {

        $fuente = $this->getFuenteDatos( $fichaTecnica );

        $stm = 'SELECT A 
                    FROM ( '. $fuente . ') AS A 
                        OFFSET ' .$offset
            . ' LIMIT ' . $limit;
        $query = \Couchbase\N1qlQuery::fromString($stm);
        $result = $this->bucket->query($query);
        return $result->rows;
    }

}