<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

use App\Entity\VariablesConfiguracion;

/**
 * VariablesConfiguracionRepository
 *
 */
class VariablesConfiguracionRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, VariablesConfiguracion::class);
    }

    public function getConfiguracionExportarPivotPdf() {
        $em = $this->getEntityManager();
        
        $conf = [];
        $variables = ['orientacion', 'formato', 'imagen', 'imagen_tipo', 'imagen_alto_cm', 'imagen_ancho_cm', 'imagen_coordenada_x_cm'];
        $valoresDefecto = ['orientacion' => 'p', 
                            'formato' => 'letter', 
                            'imagen' => 'data:image/png;base64,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', 
                            'imagen_tipo' => 'PNG', 
                            'imagen_alto_cm' => 2, 
                            'imagen_ancho_cm' => 16.5, 
                            'imagen_coordenada_x_cm' => 2.5
                        ];
        
        foreach ( $variables as $v ) {
            //Verificar si existen las variables de configuración
            $var = $em->getRepository(VariablesConfiguracion::class)->findOneByCodigo('pivot_export_pdf_' . $v); 
                        
            if ( $var == null ) {
                //Si no existe, crearla con valores por defecto
                $var = new VariablesConfiguracion();
                $var->setCodigo( 'pivot_export_pdf_' . $v );
                $var->setValor( $valoresDefecto[$v] );
                
                $em->persist($var);
                $em->flush();
            }
            $conf[ $v ] = $var;
        }
        
        return $conf;
        
    }   

}
