<?php

namespace App\Controller;

use App\Admin\FichaTecnicaAdmin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\FichaTecnica;
use App\Entity\ClasificacionUso;
use App\Entity\User;
use App\Entity\GrupoIndicadores;
use App\Entity\UsuarioGrupoIndicadores;
use App\Entity\AccesoExterno;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\SignificadoCampo;
use App\Entity\OrigenDatos;
use App\Entity\GrupoIndicadoresIndicador;

class IndicadorController extends AbstractController {

    /**
     * @return Response
     *
     * @Route("/indicadores/tablero", name="indicadores_tablero")
     */
    public function tablero() {               
        return $this->render('FichaTecnicaAdmin/tablero.html.twig');
    }   

    /**
     * @return Response
     *
     * @Route("/indicadores/pivot", name="tablero_pivotTable")
     */
    public function pivot() {               
        return $this->render('FichaTecnicaAdmin/pivot.html.twig');
    }
    
    /**
     * @Route("/profile/show", name="fos_user_profile_show")
     */
    public function raiz() {
        $this->get('session')->getFlashBag()->add(
                'notice', 'change_password.flash.success'
        );

        return $this->redirect($this->generateUrl('_inicio'));
    }

    /**
     * @Route("/indicador/dimensiones/{id}", name="indicador_dimensiones", options={"expose"=true})
     */
    public function getDimensionesAction(FichaTecnica $fichaTec) {
        $resp = array();
        $em = $this->getDoctrine()->getManager();

        if ($fichaTec) {
            $resp['nombre_indicador'] = $fichaTec->getNombre();
            $resp['id_indicador'] = $fichaTec->getId();
            $resp['unidad_medida'] = $fichaTec->getUnidadMedida();
            $resp['meta'] = $fichaTec->getMeta();
            if ($fichaTec->getCamposIndicador() != '') {
                $campos = explode(',', str_replace(array("'", ' '), array('', ''), $fichaTec->getCamposIndicador()));
            } else {
                $campos = array();
            }
            $dimensiones = array();
            foreach ($campos as $campo) {
                $significado = $em->getRepository(SignificadoCampo::class)
                        ->findOneByCodigo($campo);
                if (count($significado->getTiposGraficosArray()) > 0) {
                    $dimensiones[$significado->getCodigo()]['descripcion'] = ucfirst(preg_replace('/^Identificador /i', '', $significado->getDescripcion()));
                    $dimensiones[$significado->getCodigo()]['escala'] = $significado->getEscala();
                    $dimensiones[$significado->getCodigo()]['origenX'] = $significado->getOrigenX();
                    $dimensiones[$significado->getCodigo()]['origenY'] = $significado->getOrigenY();
                    $dimensiones[$significado->getCodigo()]['graficos'] = $significado->getTiposGraficosArray();
                }
            }
            $rangos_alertas_aux = array();
            foreach ($fichaTec->getAlertas() as $k => $rango) {
                $rangos_alertas_aux[$rango->getLimiteSuperior()]['limite_sup'] = $rango->getLimiteSuperior();
                $rangos_alertas_aux[$rango->getLimiteSuperior()]['limite_inf'] = $rango->getLimiteInferior();
                $rangos_alertas_aux[$rango->getLimiteSuperior()]['color'] = $rango->getColor()->getCodigo();
                $rangos_alertas_aux[$rango->getLimiteSuperior()]['comentario'] = $rango->getComentario();
            }
            ksort($rangos_alertas_aux);
            $rangos_alertas = array();
            foreach ($rangos_alertas_aux as $rango) {
                $rangos_alertas[] = $rango;
            }
            $resp['rangos'] = $rangos_alertas;
            $resp['formula'] = $fichaTec->getFormula();
            $resp['dimensiones'] = $dimensiones;

            //Verificar que se tiene la más antigua de las últimas lecturas de los orígenes
            //de datos del indicador
            $ultima_lectura = null;
            
            foreach ($fichaTec->getVariables() as $var) {
                $fecha_lectura = $var->getOrigenDatos()->getUltimaActualizacion();

                if ($fecha_lectura > $ultima_lectura or $ultima_lectura == null) {
                    $ultima_lectura = $fecha_lectura;
                }
            }
            
            $fichaTec->setUltimaLectura($ultima_lectura);
            //$em->flush();

            $d = $fichaTec->getUltimaLectura();
            if ($d !== null)
                $resp['ultima_lectura'] = date('d/m/Y', $d->getTimestamp());
            $resp['resultado'] = 'ok';
        } else {
            $resp['resultado'] = 'error';
        }        
        $response = new Response(json_encode($resp));

        if (getenv('APP_ENV') != 'dev') {
            $response->setMaxAge($this->getParameter('indicador_cache_consulta'));
        }

        return $response;
    }

    /**
     * @Route("/indicador/datos/mapa", name="indicador_datos_mapa", options={"expose"=true})
     */
    public function getMapaAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $dimension = $request->get('dimension');
        
        //Obtener el nombre del mapa asociado a la dimension
        $significado = $em->getRepository(SignificadoCampo::class)
                ->findOneBy(array('codigo' => $dimension));

        $mapa = $significado->getNombreMapa();
        if ($mapa != '') {
            try {
                $mapa = $this->renderView('Indicador/' . $mapa . '.json.twig');
            } catch (\Exception $e) {
                $mapa = json_encode(array('features' => ''));
            }
        } else
            $mapa = json_encode(array('features' => ''));
        
        $headers = array('Content-Type' => 'application/json');
        $response = new Response($mapa, 200, $headers);
        if (getenv('APP_ENV') != 'dev')
            $response->setMaxAge($this->getParameter('indicador_cache_consulta'));

        return $response;
    }

    /**
     * @Route("/indicador/{_locale}/change", name="change_locale")
     */
    public function changeLocaleAction($_locale, Request $request) {
        $request = $request;
        //$this->get('session')->set('_locale', $_locale);
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/tablero/usuario/change/{codigo_clasificacion}", name="change_clasificacion_uso", options={"expose"=true})
     * @ParamConverter("clasificacion", options={"mapping": {"codigo_clasificacion": "codigo"}})
     */
    public function changeClasificacionUsoAction(ClasificacionUso $clasificacion, Request $request) {
        $request = $request;
        $em = $this->getDoctrine()->getManager();
        $usuario = $this->getUser();
        $usuario->setClasificacionUso($clasificacion);
        $em->persist($usuario);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/indicador/favorito", name="indicador_favorito", options={"expose"=true})
     */
    public function indicadorFavorito(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $req = $request;

        $indicador = $em->find(FichaTecnica::class, $req->get('id'));
        $usuario = $this->getUser();
        if ($req->get('es_favorito') == 'true') {
            //Es favorito, entonces quitar
            $usuario->removeFavorito($indicador);
        } else {
            $usuario->addFavorito($indicador);
        }

        $em->flush();

        return new Response();
    }

    /**
     * @Route("/indicador/{id}/ficha", name="get_indicador_ficha", options={"expose"=true})
     */
    public function getFichaAction(FichaTecnica $fichaTec, FichaTecnicaAdmin $admin) {
        //$admin = $this->get('sonata.admin.ficha');

        $admin->setSubject($fichaTec);

        $html = $this->render($admin->getTemplate('show'), array(
            'action' => 'show',
            'object' => $fichaTec,
            'elements' => $admin->getShow(),
            'admin' => $admin,
            'base_template' => 'pdf_layout.html.twig'
        ));

        return new Response($html->getContent(), 200);
    }

    /**
     * @Route("/sala/guardar", name="sala_guardar", options={"expose"=true})
     */
    public function guardarSala(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $req = $request;
        $resp = array();

        $sala = json_decode($req->get('datos'));
        $em->getConnection()->beginTransaction();
        try {
            if ($sala->id != '') {
                $grupoIndicadores = $em->find(GrupoIndicadores::class, $sala->id);
                //Borrar los indicadores antiguos de la sala
                foreach ($grupoIndicadores->getIndicadores() as $ind)
                    $em->remove($ind);
                $em->flush();
                //$grupoIndicadores->removeIndicadore($ind);
            } else {
                $grupoIndicadores = new GrupoIndicadores();
            }

            $grupoIndicadores->setNombre($sala->nombre);
            $ahora = new \DateTime('NOW');
            $grupoIndicadores->setUpdatedAt($ahora);

            foreach ($sala->datos_indicadores as $grafico) {
                if (!empty($grafico->id_indicador)) {
                    $indG = new GrupoIndicadoresIndicador();
                    $ind = $em->find(FichaTecnica::class, $grafico->id_indicador);

                    $indG->setDimension($grafico->dimension);
                    $indG->setFiltro($grafico->filtros);
                    $indG->setFiltroPosicionDesde($grafico->filtro_desde);
                    $indG->setFiltroPosicionHasta($grafico->filtro_hasta);
                    $indG->setFiltroElementos($grafico->filtro_elementos);
                    $indG->setIndicador($ind);
                    $indG->setPosicion($grafico->posicion);
                    if (property_exists($grafico, 'orden')) {
                        $indG->setOrden($grafico->orden);
                    }
                    if (property_exists($grafico, 'vista')) {
                        $indG->setVista($grafico->vista);
                    } else {
                        $indG->setVista('grafico');
                    }
                    $indG->setTipoGrafico($grafico->tipo_grafico);
                    $indG->setGrupo($grupoIndicadores);

                    $grupoIndicadores->addIndicadore($indG);
                }
            }

            $em->persist($grupoIndicadores);
            $em->flush();

            if ($sala->id == '') {
                $usuarioGrupoIndicadores = new UsuarioGrupoIndicadores();

                $usuarioGrupoIndicadores->setUsuario($this->getUser());
                $usuarioGrupoIndicadores->setEsDuenio(true);
                $usuarioGrupoIndicadores->setGrupoIndicadores($grupoIndicadores);

                $em->persist($usuarioGrupoIndicadores);
                $em->flush();
            }
            $resp['estado'] = 'ok';
            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollback();
            $em->close();
            $resp['estado'] = 'error';
            throw $e;
        }

        $resp['id_sala'] = $grupoIndicadores->getId();

        return new Response(json_encode($resp));
    }

    /**
     * @Route("/usuario/{id}/sala/{id_sala}/{accion}", name="usuario_asignar_sala", options={"expose"=true})
     * @ParamConverter("sala", class="IndicadoresBundle:GrupoIndicadores", options={"id" = "id_sala"})
     */
    public function asignarSala(User $usuario, GrupoIndicadores $sala, $accion) {

        $em = $this->getDoctrine()->getManager();
        if ($accion == 'add') {
            $salaUsuario = new UsuarioGrupoIndicadores();
            $salaUsuario->setUsuario($usuario);
            $salaUsuario->setGrupoIndicadores($sala);
            $em->persist($salaUsuario);
        } else {
            $salaUsuario = $em->getRepository(UsuarioGrupoIndicadores::class)
                    ->findOneBy(array('usuario' => $usuario,
                'grupoIndicadores' => $sala));
            $em->remove($salaUsuario);
        }
        $em->flush();
        return new Response();
    }

    /**
     * @Route("/sala/get_imagenes/{id}/", name="sala_get_imagenes", options={"expose"=true})
     */
    public function getImagenesSala(GrupoIndicadores $sala) {
        $em = $this->getDoctrine()->getManager();
        $usuario = $this->getUser();

        $imagenes = $em->getRepository("IndicadoresBundle:Imagen")
                        ->findBy(array('sala'=>$sala, 
                            'usuario'=>$usuario));        

        $ret = '';
        foreach ($imagenes as $img) {
            $ret .= '<a href="/'.$img->getWebPath().'" class="lb_gallery"><img src="/'.$img->getWebPath().'" /></a>';
        }

        $response = new Response($ret);

        return $response;
    }
    
    /**
     * @Route("/sala/{id}/acceso_externo_crear/{duracion}/", name="crear_acceso_externo", options={"expose"=true})
     */
    public function salaCrearAccesoExterno(GrupoIndicadores $sala, $duracion) {
        $em = $this->getDoctrine()->getManager();        
        
        $accExt = new AccesoExterno();
        $date = new \DateTime();
        $date->modify("+$duracion days");       
        
        $accExt->setUsuarioCrea($this->getUser());
        $accExt->setCaducidad($date);
        $accExt->setToken(md5(rand()));
        $accExt->addSala($sala);
        
        $em->persist($accExt);
        $em->flush();
        
        $host = $this->get('request')->getSchemeAndHttpHost();
        $url = $host.'/ae/'.$accExt->getToken().'/';
        $resp = $this->get('translator')->trans('_url_acceso_ext_ayuda_').'<BR/>'.$url;
        return new Response($resp);
    }

}

//end class
