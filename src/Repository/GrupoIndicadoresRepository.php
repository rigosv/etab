<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

use App\Entity\GrupoIndicadores;

/**
 * GrupoIndicadoresRepository
 *
 */
class GrupoIndicadoresRepository extends EntityRepository
{
    public function getIndicadoresSala(GrupoIndicadores $sala)
    {
        $em = $this->getEntityManager();

        $dql = "SELECT i.filtro, i.dimension, i.posicion, i.tipoGrafico, i.orden,
                    f.id as idIndicador, i.filtroPosicionDesde, i.filtroPosicionHasta,
                    i.filtroElementos, i.vista
                    FROM App\Entity\GrupoIndicadoresIndicador i
                    JOIN i.indicador f
                    WHERE
                        i.grupo = :sala";
        $query = $em->createQuery($dql);
        $query->setParameter('sala', $sala->getId());

        return $query->getArrayResult();
    }

}
