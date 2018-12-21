<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use App\Entity\FichaTecnica;
use App\Entity\GrupoIndicadores;

use App\Entity\ComentariosSala;
use App\Entity\UsuarioGrupoIndicadores;

use App\Entity\User;
use App\Entity\SalaAcciones;

/**
 * @Route("/api/v1/tablero")
 */
class TableroSalaController extends AbstractController
{
    /**
     * @Route("/salaAccion/{id}", name="salaAccion_index", methods={"GET"})
     */
    public function salaAccionesLista(GrupoIndicadores $sala)
    {        
        $em = $this->getDoctrine()->getManager();
        $usuario = $this->getUser();
      
        $acciones = $em->getRepository(SalaAcciones::class)->findBy(array('sala'=>$sala, 'usuario'=>$usuario), array('fecha'=> 'ASC'));
        $data = [];
        foreach ($acciones as $acc){
            array_push($data, array(
                "id" => $acc->getId(),
                "fecha" => $acc->getFecha()->format('Y-m-d H:m:s'),                
                "accion" => $acc->getAcciones(),
                "observacion" => $acc->getObservaciones(),
                "responsable" => $acc->getResponsables() 
            ));
        }

        $response = [
            'status' => 200,
            'messages' => "Ok",
            'data' => $data,
            'total' => count($data)
        ];  

        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        // devolver la respuesta en json             
        return new Response($serializer->serialize($response, "json"));
    }
    
    /**
     * @Route("/salaAccion/{id}", name="salaAccion", methods={"POST"})
     */
    public function guardarAccion(GrupoIndicadores $sala, Request $request ) {
        $em = $this->getDoctrine()->getManager();
        $usuario = $this->getUser();
        $resp = array();

        $datos = (object) $request->request->all();
        
        $salaAcciones = new SalaAcciones();
        $salaAcciones->setSala($sala);
        $salaAcciones->setAcciones($datos->acciones);
        $salaAcciones->setObservaciones($datos->observaciones);
        $salaAcciones->setResponsables($datos->responsables);
        $salaAcciones->setFecha(new \DateTime());
        $salaAcciones->setUsuario($usuario);
        
        $em->getConnection()->beginTransaction();
        try {
            $em->persist($salaAcciones);
            $em->flush();            
            $em->getConnection()->commit();

            $acciones = $em->getRepository(SalaAcciones::class)->findBy(array('sala'=>$sala, 'usuario'=>$usuario), array('fecha'=> 'ASC'));

            $data = [];
            foreach ($acciones as $acc){
                array_push($data, array(
                    "id" => $acc->getId(),
                    "fecha" => $acc->getFecha()->format('Y-m-d H:m:s'),                
                    "accion" => $acc->getAcciones(),
                    "observacion" => $acc->getObservaciones(),
                    "responsable" => $acc->getResponsables() 
                ));
            }


            $response = [
                'status' => 200,
                'messages' => "Ok",
                'data' => $data,
                'total' => count($data)
            ];  
        } catch (\Exception $e) {
            $response = [
                'status' => 500,
                'messages' => $e->getMessage(),
                'data' => [],
            ];   
        }
        
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Route("/usuariosSala/{idSala}", name="usuariosSala", methods={"GET"})
     */
    public function usuariosSala($idSala) {
        $em = $this->getDoctrine()->getManager();
        
        $usuarios_asignados = $em->getRepository(UsuarioGrupoIndicadores::class)->findBy(array('grupoIndicadores' => $idSala));
        $usuarios_asignados_por_usuario_actual = $em->getRepository(UsuarioGrupoIndicadores::class)->findBy(array('usuario'=>$this->getUser(), 'grupoIndicadores' => $idSala));
        $usuarios_sala_por_usuario_actual = array(); 
        foreach ($usuarios_asignados_por_usuario_actual as $ua){
            $usuarios_sala_por_usuario_actual[] = $ua->getUsuario()->getId();
        }
        
        $usuarios_sala = array(); 
        foreach ($usuarios_asignados as $ua){
            $usuarios_sala[] = $ua->getUsuario()->getId();
        }
        
        $usuarios = $em->getRepository(User::class)->findBy(array(), array('username'=>'ASC'));
        
        $data = [];
        foreach ($usuarios as $u){
            if ($u->getId() != $this->getUser()->getId()){
                $name = $u->getFirstname().$u->getLastname() == "" ? $u->getUsername() : $u->getFirstname().' '.$u->getLastname();
                $selected = (in_array($u->getId(), $usuarios_sala)) ? true : false;
                array_push($data, array(
                    "id" => $u->getId(),
                    "nombre" => $name,
                    "selected" => $selected,
                ));
            }
        }
        $response = [
            'status' => 200,
            'messages' => 'Ok',
            'data' => $data
        ];   

        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Route("/comentarioSala/{idSala}", name="comentarioSala_index", methods={"GET"})
     */
    public function comentarioSala($idSala, Request $request) {
        $session = new Session();
        $em = $this->getDoctrine()->getManager();
        $req = $request;
                                    
        if ($req->get('vez') == 1 or $session->get('ultima_lectura_comentarios_sala') == ''){
            $comentarios = $em->createQuery('SELECT c, u FROM App\Entity\ComentariosSala c 
                LEFT JOIN c.usuario u 
                WHERE c.sala = :sala AND c.fecha > :fecha ORDER  BY c.fecha ASC')
            ->setParameter('sala', $idSala)->setParameter('fecha', '1970-01-01')->getResult();                        
        }
        else{            
            $comentarios = $em->createQuery('SELECT c, u FROM App\Entity\ComentariosSala c 
                LEFT JOIN c.usuario u  
                WHERE c.sala = :sala AND c.fecha > :fecha ORDER  BY c.fecha ASC')
            ->setParameter('sala', $idSala)->setParameter('fecha', $session->get('ultima_lectura_comentarios_sala'))->getResult();                        
        }
        
        $session->set('ultima_lectura_comentarios_sala', new \DateTime("now"));
        
        $ret = ''; $data = [];
        foreach($comentarios as $comentario){
            $u = $comentario->getUsuario();
            $photo  = $u->getPhoto() != "" ? $u->getPhoto() : $this->container->get('templating.helper.assets')->getUrl('bundles/indicadores/images/user.png');
            $name = $u->getFirstname().$u->getLastname() == "" ? $u->getUsername() : $u->getFirstname().' '.$u->getLastname();
            $ret .= '<li>
                        <div class="comment-main-level"> 
                            <!-- Avatar -->
                            <div class="comment-avatar"><img src="'.$photo.'"></div>                                               
                            <!-- Contenedor del Comentario -->
                            <div class="comment-box">
                                <div class="comment-head">
                                    <h6 class="comment-name"><a href="#">'.$name.'</a></h6>
                                    
                                    <i class="fa fa-calendar"><span>'.$comentario->getFecha()->format('d-M-Y H:i:s').'</span></i>
                                    
                                </div>
                                <div class="comment-content">
                                    '.$comentario->getComentario().'
                                </div>
                            </div>
                        </div>
                        
                    </li>';
            array_push($data, array(
                "id" => $comentario->getId(),
                "fecha" => $comentario->getFecha()->format('Y-m-d H:m:s'),                
                "comentario" => $comentario->getComentario(),
                "foto" => $photo,
                "nombre" => $name 
            ));
        }
        $response = [
            'status' => 200,
            'messages' => "Ok",
            'data' => $data,
            'html' => $ret,
            'total' => count($comentarios)
        ];
                
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        return new Response($serializer->serialize($response, "json"));                  
    }

    /**
     * @Route("/comentarioSala/{sala}", name="comentarioSala", methods={"POST"})
     */
    public function comentarioSalaGuardar(GrupoIndicadores $sala, Request $request) {
        $session = new Session();
        $em = $this->getDoctrine()->getManager();
        $req = $request;
        $usua = $this->getUser();
        $comentario = new ComentariosSala();
        $ahora = new \DateTime("now");
        $ret = ""; $msg = "";
        if($req->get('comentarios')!="")
        {
            $comentario->setComentario($req->get('comentarios'));
            $comentario->setUsuario($this->getUser());
            $comentario->setFecha($ahora);
            $comentario->setSala($sala);
            
            $em->persist($comentario);
            $em->flush();

            $session->set('ultima_lectura_comentarios_sala', new \DateTime("now"));
            
            $ret = '<li>
                        <div class="comment-main-level"> 
                            <!-- Avatar -->
                            <div class="comment-avatar"></div>                                               
                            <!-- Contenedor del Comentario -->
                            <div class="comment-box">
                                <div class="comment-head">
                                    <h6 class="comment-name"><a href="#">'.$comentario->getUsuario().'</a></h6>
                                    
                                    <i class="fa fa-calendar"><span>'.$comentario->getFecha()->format('d-M-Y H:i:s').'</span></i>
                                    
                                </div>
                                <div class="comment-content">
                                    '.$comentario->getComentario().'
                                </div>
                            </div>
                        </div>
                        
                    </li>';
        }
        if(count($req->get("usuarios_con_cuenta")))
        {
            $dato = array(array
            (
                'token' =>md5(time()), 
                'sala' => $sala->getId()
            ));
            $usuarios_con = $req->get("usuarios_con_cuenta");
            if(count($usuarios_con) > 0 && $usuarios_con[0] == 'All'){
                $qb = $em->createQueryBuilder();
                $qb->select('u');
                $qb->from('App\Entity\User', 'u');
                $users = $qb->getQuery()->getResult();
                $usu = false;
            }else{
                $usu = true;
                $users = $req->get("usuarios_con_cuenta");
            }            

            foreach($users as $user)
            {  
                $userId = $usu ? $user->id : $user->getId();
                $usuario = $em->getRepository(User::class)->findOneBy(array('id' => $userId));     
                if($usuario->isEnabled())
                { 
                    $name = $usuario->getFirstname().$usuario->getLastname() == "" ? $usuario->getUsername() : $usuario->getFirstname().' '.$usuario->getLastname();                      
                    $array = array(array
                    (
                        'username' => $usuario->getUsername(), 
                        'email' => $usuario->getEmail(), 
                        'nombre' => $usuario->getFirstname(), 
                        'apellido' => $usuario->getLastname()
                    )); 
                    $documento1 = $this->container->getParameter('kernel.root_dir').'/../web/bundles/indicadores/images/logo_salud.png';
                    $message = \Swift_Message::newInstance()
                        ->attach(\Swift_Attachment::fromPath($documento1))
                        ->setSubject('Sala eTAB')
                        ->setFrom('eTAB@SM2015.com.mx')
                        ->setTo($usuario->getEmail()) 
                        ->setBody($this->renderView('/Page:sala.html.twig', array('dato' => $dato,'array' => $array)),"text/html");
                    $this->get('mailer')->send($message);
                    $msg.="se envio correo a: ".$name." (".$usuario->getEmail().")\n\n";
                }
            }            
        }

        if($req->get("usuarios_sin_cuenta")!="")
        {
            $token  = md5(time());
            $dato = array(array
            (
                'token' =>$token, 
                'sala' => $sala->getId()
            ));
            
            $usuario = explode(",", $req->get("usuarios_sin_cuenta"));

            for($i=0; $i<count($usuario);$i++)
            {       
                if(stripos($usuario[$i],"@"))
                { 
                    $array = array(array
                    (
                        'username' => "Temporal", 
                        'email' => $usuario[$i], 
                        'nombre' => "", 
                        'apellido' => ""
                    )); 
                    $documento1 = $this->container->getParameter('kernel.root_dir').'/../web/bundles/indicadores/images/logo_salud.png';
                    $message = \Swift_Message::newInstance()
                        ->attach(\Swift_Attachment::fromPath($documento1))
                        ->setSubject('Sala eTAB')
                        ->setFrom('eTAB@SM2015.com.mx')
                        ->setTo(trim($usuario[$i])) 
                        ->setBody($this->renderView('Page/sala.html.twig', array('dato' => $dato,'array' => $array)),"text/html");
                    $this->get('mailer')->send($message);
                    $msg.="se envio correo a: ".$usuario[$i]."\n\n";
                }
            }

            if($msg!="")
            {
                $social = new Social();
                $ahora = new \DateTime("now");
                
                $social->setToken($token);
                $social->setCreado($ahora);
                $social->setSala($sala);
                
                $em->persist($social);
                $em->flush();
            }    
        }

        $response = [
            'status' => 200,
            'messages' => "Ok",
            'data' => [],
            'html' => $ret,
            "correo" => $msg
        ];
                
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        return new Response($serializer->serialize($response, "json"));
    }
}
