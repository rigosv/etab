<?php

namespace App\Repository;

use App\Entity\ConfiguracionPivotTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;


class ConfiguracionPivotTableRepository extends ServiceEntityRepository {

    private $cnxDatos;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ConfiguracionPivotTable::class);
    }


    
    public function setConfPivoTable($tipoElemento, $idElemento, $idUsuario, $configuracion, $nombre, $esPorDefecto = false) {
        $em = $this->getEntityManager();
        
        $em->getConnection()->executeQuery($sql);
        
        //verificar si existe la configuración
        $sql = "SELECT configuracion 
                    FROM configuracion_pivot_table 
                    WHERE tipo_elemento = '$tipoElemento' 
                        AND identificador_elemento = '$idElemento'
                        AND id_usuario = $idUsuario ";
        $cons = $em->getConnection()->executeQuery($sql);
        
        if ($cons->rowCount() > 0){
            //Actualizar la configuración
            $sql = "UPDATE configuracion_pivot_table SET configuracion = '$configuracion'
                        WHERE tipo_elemento = '$tipoElemento' 
                        AND identificador_elemento = '$idElemento'
                        AND id_usuario = $idUsuario ";
            $em->getConnection()->executeQuery($sql);
        } else {
            //Guardar la configuración
            $sql = "INSERT INTO configuracion_pivot_table(tipo_elemento, identificador_elemento, configuracion, id_usuario)
                        VALUES ('$tipoElemento', '$idElemento', '$configuracion', $idUsuario) ";
            $em->getConnection()->executeQuery($sql);
        }
        //echo $sql;
        
    }
    
    public function getConfPivoTable($tipoElemento, $idElemento, $idUsuario) {
        $em = $this->getEntityManager();
        
        //Recuperar la configuración
        $sql = "SELECT configuracion 
                    FROM Configuracion_pivot_table 
                    WHERE tipo_elemento = '$tipoElemento' 
                        AND identificador_elemento = '$idElemento'
                        AND id_usuario = $idUsuario ";

        return $em->getConnection()->executeQuery($sql)->fetch();
    }
}