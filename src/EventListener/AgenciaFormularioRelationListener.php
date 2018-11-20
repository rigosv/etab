<?php


namespace App\EventListener;


use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AgenciaFormularioRelationListener
{

    private $container;

    public function __construct( ContainerInterface $container )
    {
        $this->container = $container;
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {

        // the $metadata is the whole mapping info for this class
        $metadata = $eventArgs->getClassMetadata();

        if ($metadata->getName() != 'App\Entity\AGencia' or !array_key_exists('GridFormBundle' , $this->container->getParameter('kernel.bundles')) ) {
            return;
        }

        $metadata->mapManyToMany(array(
            'targetEntity'  => MINSAL\Bundle\GridFormBundle\Entity\Formulario::class,
            'fieldName'     => 'formularios',
            'cascade'       => array('persist'),
            'joinTable'     => array(
                'name'        => 'indicador_formulario',
                'joinColumns' => array(
                    array(
                        'name'                  => 'id_agencia',
                        'referencedColumnName'  => 'id',
                        'onDelete'  => 'CASCADE',
                        'onUpdate'  => 'CASCADE',
                    ),
                ),
                'inverseJoinColumns'    => array(
                    array(
                        'name'                  => 'id_formulario',
                        'referencedColumnName'  => 'id',
                        'onDelete'  => 'CASCADE',
                        'onUpdate'  => 'CASCADE',
                    ),
                )
            )
        ));

    }
}