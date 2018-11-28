<?php

namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

use App\Entity\FichaTecnica;
use App\Entity\OrigenDatos;
use App\Message\SmsCargarOrigenDatos;


class CargarOrigenDatoCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('origen-dato:cargar')
            ->setDescription('Cargar datos especificados en los orígenes')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $bus = $this->getContainer()->get('message_bus');

        //Recuperar todos las fichas técnicas de indicadores
        $indicadores = $em->getRepository(FichaTecnica::class)->findAll();

        $fecha = new \DateTime("now");
        $ahora = $fecha;

        foreach ($indicadores as $ind) {

            if ($ind->getUltimaLectura() == null)
                $dif = 1; // No se ha realizado carga de datos antes, mandar a cargarlos
            else {
                $ultima_lectura = $ind->getUltimaLectura();
                if ($ind->getPeriodo() != null)
                    $periocidad = $ind->getPeriodo()->getCodigo();
                else $periocidad='d';

                $intervalo = $ahora->diff($ultima_lectura);
                $dif_dias = $intervalo->format('%a');

                if ($periocidad == 'd') //Diario?
                    $dif = $dif_dias;
                elseif ($periocidad == 'sm') //semanal?
                    $dif = $dif_dias / 7;
                elseif ($periocidad == 'm')
                    $dif = $dif_dias / 30; //mensual?
                elseif ($periocidad == 't')
                    $dif = $dif_dias / 30 * 3; //trimestral?
                elseif ($periocidad == 's')
                    $dif = $dif_dias / 30 * 6; //semestral?
                elseif ($periocidad == 'a')
                    $dif = $dif_dias / 365; //Anual?
                else
                    $dif = 1; // No tiene periocidad, cargarlo
            }
            if ($dif >= 1) {
                //Es necesaria realizar la carga de datos
                // Recuperar los orígenes de datos asociados a las variables del indicador
                foreach ($ind->getVariables() as $var) {
                    $origenDato = $var->getOrigenDatos();
                    // Solo los orígenes desde base de datos se cargarán periodicamente
                    // los que sean de archivos solo a demanda
                    if ($origenDato->getSentenciaSql() != '') {

                        $carga_directa = $origenDato->getEsCatalogo();
                        // No mandar a la cola de carga los que son catálogos, Se cargarán directamente
                        if ($carga_directa){
                            $em->getRepository(OrigenDatos::class)->cargarCatalogo($origenDato);
                            $ind->setUltimaLectura($ahora);
                        }
                        else {
                            $bus->dispatch(new SmsCargarOrigenDatos($origenDato->getId()));
                        }
                    }
                }
            }
        }
        $em->flush();
    }

}