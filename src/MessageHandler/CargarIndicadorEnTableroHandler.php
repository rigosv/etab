<?php

namespace App\MessageHandler;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

use App\AlmacenamientoDatos\AlmacenamientoProxy;
use App\Entity\FichaTecnica;
use App\Message\SmsCargarIndicadorEnTablero;




class CargarIndicadorEnTableroHandler implements MessageHandlerInterface
{

    private $em;
    private $almacenamiento;

    public function __construct(EntityManagerInterface $em, AlmacenamientoProxy $almacenamiento)
    {
        $this->em = $em;
        $this->almacenamiento = $almacenamiento;
    }

    public function __invoke(SmsCargarIndicadorEnTablero $message )
    {
        $idIndicador = $message->getIdIndicador();
        $repository = $this->em->getRepository(FichaTecnica::class);

        $fichaTec = $this->em->find(FichaTecnica::class, $idIndicador);

        if ($fichaTec != null ) {

            $repository->crearCamposIndicador($fichaTec);
            if (!$fichaTec->getEsAcumulado()) {
                $this->almacenamiento->crearIndicador($fichaTec);
            }
        }
    }
}