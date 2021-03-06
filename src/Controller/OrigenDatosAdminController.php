<?php

namespace App\Controller;

use App\Message\SmsCargarOrigenDatos;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\RegistryInterface;

use App\Entity\OrigenDatos;



class OrigenDatosAdminController extends Controller
{
    public function batchActionCrearPivoteIsRelevant(array $normalizedSelectedIds, $allEntitiesSelected)
    {
        return $this->batchActionMergeIsRelevant($normalizedSelectedIds, $allEntitiesSelected, true);
    }

    public function batchActionCrearPivote(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->batchActionMerge($selectedModelQuery, true);
    }

    public function batchActionMergeIsRelevant(array $normalizedSelectedIds, $allEntitiesSelected, $esPivote=false)
    {
        $em = $this->getDoctrine()->getManager();
        $parameterBag = $this->get('request')->request;
        $selecciones = $parameterBag->get('idx');
        // Verificar que los orígenes esten configurados
        foreach ($selecciones as $id_origen) {
            $origenDato = $em->find(OrigenDatos::class, $id_origen);
            if ($origenDato->getEsCatalogo())
                return $this->get('translator')->trans('fusion.no_catalogos');
            if ($origenDato->getEsFusionado())
                return $this->get('translator')->trans('fusion.no_fusionados');
            $configurado = $em->getRepository('IndicadoresBundle:OrigenDatos')->estaConfigurado($origenDato);
            if (!$configurado)
                return $origenDato->getNombre() . ': ' . $this->get('translator')->trans('origen_no_configurado');
        }
        if (count($selecciones) > 1)
            return true;
        else
            return $this->get('translator')->trans('fusion.selecione_2_o_mas_elementos');
    }

    public function batchActionMerge(ProxyQueryInterface $selectedModelQuery, $esPivote = false, Request $request)
    {
        $selecciones = $request->get('idx');
        $em = $this->getDoctrine()->getManager();

        //Obtener la información de los campos de cada origen
        $origen_campos = array();
        foreach ($selecciones as $k => $origen) {
            $origenDato[$k] = $em->find(OrigenDatos::class, $origen);
            foreach ($origenDato[$k]->getCampos() as $campo) {
                //La llave para considerar campo comun será el mismo tipo y significado
                $llave = $campo->getSignificado()->getId() . '-' . $campo->getTipoCampo()->getId();
                $origen_campos[$origen][$llave]['id'] = $campo->getId();
                $origen_campos[$origen][$llave]['nombre'] = $campo->getNombre();
                $origen_campos[$origen][$llave]['significado'] = $campo->getSignificado()->getCodigo();
                $origen_campos[$origen][$llave]['idSignificado'] = $campo->getSignificado()->getId();
                $origen_campos[$origen][$llave]['idTipo'] = $campo->getTipoCampo()->getId();
            }
        }

        //Determinar los campos comunes (con igual significado)
        $aux = $origen_campos;
        $campos_comunes = array_shift($aux);
        foreach ($aux as $a) {
            $campos_comunes = array_intersect_key($campos_comunes, $a);
        }

        //Dejar solo los campos que son comunes
        foreach ($origen_campos as $k => $campos) {
            $origen_campos[$k] = array_intersect_key($campos, $campos_comunes);
        }

        // Ordenar y darle la estructura para mostar en la plantilla
        $campos_ord = array();
        foreach ($campos_comunes as $sig_tipo => $c) {
            $campos_ord[$sig_tipo]['value']['nombre'] = $c['significado'];
            $campos_ord[$sig_tipo]['value']['idSignificado'] = $c['idSignificado'];
            $campos_ord[$sig_tipo]['value']['idTipo'] = $c['idTipo'];

            $campos_ord[$sig_tipo]['nombre'] = $c['significado'];
            $campos_ord[$sig_tipo]['value']['origenes_a_Fusionar'] = '';
            foreach ($origen_campos as $origen => $campos) {
                $campos_ord[$sig_tipo]['datos'][$origen] = $campos[$sig_tipo];
            }
            $campos_ord[$sig_tipo]['value']['origenes_a_Fusionar'] = trim($campos_ord[$sig_tipo]['value']['origenes_a_Fusionar'], ',');
            $campos_ord[$sig_tipo]['value'] = json_encode($campos_ord[$sig_tipo]['value']);
        }

        return $this->renderView('OrigenDatosAdmin/merge_selection.html.twig', array('origen_dato' => $origenDato,
            'campos' => $campos_ord,
            'es_pivote' => $esPivote
        ));
    }


    public function batchActionLoadDataIsRelevant(array $selectedIds, $allEntitiesSelected, Request $request = null)
    {
        $em = $this->getDoctrine()->getManager();
        $parameterBag = $request->request;

        //if ( !$parameterBag->get('all_elements')) {
        if ( $allEntitiesSelected ) {
            $origenes = $em->getRepository(OrigenDatos::class)->findAll();
            foreach ($origenes as $origen) {
                $configurado = $em->getRepository(OrigenDatos::class)->estaConfigurado($origen);
                if (!$configurado)
                    return $origen->getNombre() . ': ' . $this->get('translator')->trans('origen_no_configurado');
            }
        } else {
            $selecciones = $selectedIds;
            // Verificar que los orígenes esten configurados
            foreach ($selecciones as $id_origen) {
                $origenDato = $em->find(OrigenDatos::class, $id_origen);
                $configurado = $em->getRepository(OrigenDatos::class)->estaConfigurado($origenDato);
                if (!$configurado)
                    return $origenDato->getNombre() . ': ' . $this->get('translator')->trans('origen_no_configurado');
            }
        }

        return true;
    }

    public function batchActionLoadData(ProxyQueryInterface $selectedModelQuery, Request $request)
    {
        $almacenamiento = $this->get('almacenamiento_datos');

        $bus = $this->get('message_bus');


        //Mardar a la cola de carga de datos cada origen seleccionado
        $parameterBag = $request->request;

        $em = $this->getDoctrine()->getManager();

        $fecha = new \DateTime("now");
        $ahora = $fecha;

        if (!$parameterBag->get('all_elements')){
            $selecciones = $selectedModelQuery->execute();
        }
        else{
            $selecciones = $em->getRepository(OrigenDatos::class)->findAll();
        }

        foreach ($selecciones as $origenDato) {


            // Recuperar el nombre y significado de los campos del origen de datos
            $campos_sig = array();
            $campos = $origenDato->getCampos();
            foreach ($campos as $campo) {
                $campos_sig[$campo->getNombre()] = $campo->getSignificado()->getCodigo();
            }

            foreach ($origenDato->getVariables() as $var) {
                foreach ($var->getIndicadores() as $ind) {
                    $ind->setUltimaLectura($ahora);
                }
            }
            $em->flush();
            $carga_directa = $origenDato->getEsCatalogo();
            // No mandar a la cola de carga los que son catálogos, Se cargarán directamente
            if ($carga_directa) {
                $ruta = $this->getParameter('app.upload_directory');
                $nombre = $origenDato->getArchivoNombre();
                $phpexcel = $this->get('phpspreadsheet');
                $em->getRepository(OrigenDatos::class)->cargarCatalogo($origenDato, $ruta, $nombre, $phpexcel);
            } else
                $bus->dispatch(new SmsCargarOrigenDatos($origenDato->getId()));
        }
        $this->addFlash('sonata_flash_success', $this->get('translator')->trans('flash_batch_load_data_success'));

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }

    public function mergeSaveAction(Request $request)
    {
        $req = $request;
        $opciones = $req->get('fusionar');
        $em = $this->getDoctrine()->getManager();

        //Crear el origen
        $origenDato = new OrigenDatos();
        $origenDato->setNombre($req->get('nombre'));
        $origenDato->setDescripcion($req->get('descripcion'));
        if ($req->get('es_pivote') == 1)
            $origenDato->setEsPivote(true);
        else
            $origenDato->setEsFusionado(true);

        foreach ($req->get('origenes_fusionados') as $k => $origen_id) {
            $origenFu = $em->find(OrigenDatos::class, $origen_id);
            $origenDato->addFusione($origenFu);
        }

        $campos_fusionados = '';
        foreach ($req->get('campos_fusionar') as $campo) {
            $obj = json_decode($campo);
            $campos_fusionados .= "'" . $obj->nombre . "',";
        }
        $campos_fusionados = trim($campos_fusionados, ',');
        $origenDato->setCamposFusionados($campos_fusionados);

        $em->persist($origenDato);
        $em->flush();

        if ($req->get('es_pivote') == 1)
            $this->addFlash('sonata_flash_success', $origenDato->getNombre() . ' ' . $this->get('translator')->trans('_origen_pivote_creado_'));
        else
            $this->addFlash('sonata_flash_success', $origenDato->getNombre() . ' ' . $this->get('translator')->trans('fusion.origen_fusionado_creado'));

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }

    public function loadDataAction(Request $request)
    {
        $id = $request->get('id');
        $origen = $this->getDoctrine()->getManager()->find(OrigenDatos::class, $id);
        //$valid = $this->batchActionLoadDataIsRelevant(array($id), false, $request);
        //if($valid === true)

        return $this->batchActionLoadData(array($id), $request);
        /*else {
            $this->addFlash('sonata_flash_error', $origen->getNombre() . ': ' . $this->get('translator')->trans('origen_no_configurado'));

            return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
        }*/
    }

    public function poscargaAction(OrigenDatos $origen, Request $request, RegistryInterface $registry)
    {
        $cnx = $registry->getManager('etab_datos')->getConnection();
        $resul = [];

        if ($origen->getAccionesPoscarga() != null ){
            $acciones = explode( ';', $origen->getAccionesPoscarga());
            $error = false;
            foreach ( $acciones as $a ) {
                if ( !$error and trim($a) != '') {
                    //Quitar las líneas de comentario
                    try{
                        $q = $cnx->query($a);
                        $r = $q->rowCount();
                    }catch ( \Exception $e ){
                        $r = $e->getMessage();
                    }
                    $resul[] = ['accion' => nl2br($a), 'resultado' => $r];
                }
            }
        }

        return $this->renderWithExtraParams('OrigenDatosAdmin/poscarga.html.twig', array(
            'resultado' => $resul
        ));
    }

}