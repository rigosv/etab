<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\Conexion;
use Doctrine\DBAL as DBAL;

/**
 * ConexionRepository
 *
 */
class ConexionRepository extends EntityRepository {

    public function getConexionGenerica(Conexion $conexion) {
        if ($conexion->getIdMotor()->getCodigo() == 'pdo_dblib') {
            $servername = $conexion->getIp();
            if ($conexion->getPuerto() != '')
                $servername .= ',' . $conexion->getPuerto();
            $conn = mssql_connect($servername, $conexion->getUsuario(), $conexion->getClave());
            mssql_select_db($conexion->getNombreBaseDatos(), $conn);
        } else {
            // Construir el Conector genérico
            $config = new DBAL\Configuration();

            $connectionParams = array(
                'dbname' => $conexion->getNombreBaseDatos(),
                'user' => $conexion->getUsuario(),
                'password' => $conexion->getClave(),
                'host' => $conexion->getIp(),
                'driver' => $conexion->getIdMotor()->getCodigo()
            );
            try {
                if ($conexion->getIdMotor()->getCodigo() == 'pdo_mysql' or $conexion->getIdMotor()->getCodigo() == 'pdo_pgsql') {
                    $motor = explode('_', $conexion->getIdMotor()->getCodigo());
                    $cadena = $motor[1].':host='.$conexion->getIp().';dbname='.$conexion->getNombreBaseDatos();                                        
                }
                if ($conexion->getPuerto() != '' and $conexion->getIdMotor()->getCodigo() != 'pdo_sqlite') {
                    $connectionParams['port'] = $conexion->getPuerto();
                    $cadena .= ';port='.$conexion->getPuerto();
                }
                $dbh = new \PDO($cadena, $conexion->getUsuario(), $conexion->getClave());
                $connectionParams['pdo'] = $dbh;
            
                $conn = DBAL\DriverManager::getConnection($connectionParams, $config);
                $conn->connect();
            } catch (DBAL\DBALException $e) {
                echo ' Error 1 en conexión ' . $e->getMessage();
                return false;
            } catch (\Exception $e) {
                echo ' Error 2 en conexión ' . $e->getMessage();
                return false;
            } catch (\ErrorException $e) {
                echo ' Error 3 en conexión ' . $e->getMessage();
                return false;
            } catch (\PDOException $e) {
                echo ' Error 4 en conexión ' . $e->getMessage();
                return false;
            }
        }

        return $conn;
    }

}
