<?php


namespace App\EventListener;


use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserEstructuraRelationListener
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

        if ($metadata->getName() != 'App\Entity\User' or !array_key_exists('CostosBundle' , $this->container->getParameter('kernel.bundles')) ) {
            return;
        }

        $metadata->mapManyToOne(array(
            'targetEntity'  => \MINSAL\Bundle\CostosBundle\Entity\Estructura::class,
            'fieldName'     => 'establecimientoPrincipal',
            'joinColumns' => array(
                array(
                    'name' => 'establecimientoprincipal_id',
                    'referencedColumnName' => 'id'
                )
            ),
        ));

    }
}