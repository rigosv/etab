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

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

use App\Entity\Social;
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
     * 
     * @SWG\Get(
     *      tags={"Tablero Social"},
     *      summary="Lista de acciones",
     *      description="Lista de las acciones del indicador",
     *      produces={"application/json"},
     *      @SWG\Parameter(parameter="id_in_path", name="id", description="id de la sala", required=true, type="integer", in="path")
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="Regresa objecto actualizado"     
     *  ),
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
     * 
     * @SWG\Post(
     *      tags={"Tablero Social"},
     *      summary="Guardar acciones",
     *      description="Guarda las acciones descritas para la sala",
     *      produces={"application/json"},  
     *      @SWG\Parameter(parameter="ficha_in_path", name="id", description="id de la sala", required=true, type="string", in="path"),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JSON con los filtros",
     *          type="object",
     *          format="application/json",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="acciones", type="string", example="Ejemplo"),
     *              @SWG\Property(property="observaciones", type="string", example="algo de la sala"),
     *              @SWG\Property(property="responsables", type="string", example="Algun usuario")
     *          )
     *      )
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="Regresa objecto actualizado"     
     *  ),
     * 
     *  @SWG\Response(
     *     response=500,
     *     description="Regresa un error ocurrido en el servidor"     
     *  ),
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
     * 
     * @SWG\Get(
     *      tags={"Tablero Social"},
     *      summary="Lista de usuarios sala",
     *      description="Lista los usuarios que pertenecen a una sala",
     *      produces={"application/json"},
     *      @SWG\Parameter(parameter="id_in_path", name="idSala", description="id de la sala", required=true, type="integer", in="path")
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="Regresa objecto actualizado"     
     *  ),
     */
    public function usuariosSala($idSala) {
        $em = $this->getDoctrine()->getManager();
        
        $data = [];
        if ($this->getUser() != null ){
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
     * 
     * @SWG\Get(
     *      tags={"Tablero Social"},
     *      summary="Comentarios de sala",
     *      description="Lista los comentarios de una sala",
     *      produces={"application/json"},
     *      @SWG\Parameter(parameter="id_in_path", name="idSala", description="id de la sala", required=true, type="integer", in="path")
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="Regresa objecto actualizado"     
     *  ),
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
        if ( count($comentarios) == 0 ){
            $comentarios = $em->createQuery('SELECT c, u FROM App\Entity\ComentariosSala c 
                LEFT JOIN c.usuario u  
                WHERE c.sala = :sala ORDER  BY c.fecha DESC')
            ->setParameter('sala', $idSala)->setMaxResults(20)->getResult();
        }
        
        $session->set('ultima_lectura_comentarios_sala', new \DateTime("now"));
        
        $ret = ''; $data = [];
        foreach($comentarios as $comentario){
            $u = $comentario->getUsuario();
            $photo = '';
            //$photo  = $u->getPhoto() != "" ? $u->getPhoto() : $this->container->get('templating.helper.assets')->getUrl('bundles/indicadores/images/user.png');
            $name = $u->getFirstname().$u->getLastname() == "" ? $u->getUsername() : $u->getFirstname().' '.$u->getLastname();
            $ret .= '<li>
                        <div class="comment-main-level"> 
                            <!-- Avatar -->
                            <!-- <div class="comment-avatar"><img src="'.$photo.'"></div> -->
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
     * 
     * @SWG\Post(
     *      tags={"Tablero Social"},
     *      summary="Guardar comentarios",
     *      description="Guarda los comengtarios para la sala y las envia al correo de las personas compartidas",
     *      produces={"application/json"},  
     *      @SWG\Parameter(parameter="ficha_in_path", name="sala", description="id de la sala", required=true, type="string", in="path"),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JSON con los filtros",
     *          type="object",
     *          format="application/json",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="comentarios", type="string", example="Ejemplo de comentarios"),
     *              @SWG\Property(property="es_permanente", type="boolean", example=true),
     *              @SWG\Property(property="tiempo_dias", type="string", example="2"),
     *              @SWG\Property(property="usuarios_sin_cuenta", type="array", 
     *                  @SWG\Items(example="ramirez.esquinca@gmail.com")
     *              ),@SWG\Property(property="usuarios_con_cuenta", type="array", 
     *                  @SWG\Items(example="1")
     *              )
     *          )
     *      )
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="Regresa objecto actualizado"     
     *  ),
     * 
     *  @SWG\Response(
     *     response=500,
     *     description="Regresa un error ocurrido en el servidor"     
     *  ),
     */
    public function comentarioSalaGuardar(GrupoIndicadores $sala, Request $request, \Swift_Mailer $mailer) {        
        $session = new Session();
        $em = $this->getDoctrine()->getManager();
        $req = $request;
        $usua = $this->getUser();
        $comentario = new ComentariosSala();
        $ahora = new \DateTime("now");
        $ret = ""; $msg = "";

        $datos = (object) $request->request->all();

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
        if($req->get("usuarios_con_cuenta") != "")
        {
            $dato = array(array
            (
                'token' =>md5(time()), 
                'sala' => $sala->getId(),
                'nombre_sala' => $sala->getNombre()
            ));
            $usuarios_con = $req->get("usuarios_con_cuenta");
            if(count($usuarios_con) > 0 && $usuarios_con[0] == 'All'){
                $qb = $em->createQueryBuilder();
                $qb->select('u');
                $qb->from('App\Entity\User', 'u');
                $users = $qb->getQuery()->getResult();
            }else{
                $qb = $em->createQueryBuilder();
                $qb->select('u');
                $qb->from('App\Entity\User', 'u');
                $qb->where('u.id IN (:usuarios)');
                $qb->setParameter('usuarios', $usuarios_con);
                $users = $qb->getQuery()->getResult();
            }            

            foreach($users as $usuario)
            {  
                //$userId = $user->getId();
                //$usuario = $em->getRepository(User::class)->findOneBy(array('id' => $userId));
                
                if($usuario->isEnabled())
                { 
                    //Verificar que no esté ya asignado
                    $userSala = $em->getRepository(UsuarioGrupoIndicadores::class)->findOneBy(['grupoIndicadores' => $sala, 'usuario' => $usuario]);
                    if ( $userSala === null ){
                        $userSala = new UsuarioGrupoIndicadores();
                        $userSala->setEsDuenio(false);
                        $userSala->setGrupoIndicadores($sala);
                        $userSala->setUsuario($usuario);
                        $userSala->setUsuarioAsigno($this->getUser());
                        $em->persist($userSala);
                    }
                    $name = $usuario->getFirstname().$usuario->getLastname() == "" ? $usuario->getUsername() : $usuario->getFirstname().' '.$usuario->getLastname();                      
                    $array = array(array
                    (
                        'username' => $usuario->getUsername(), 
                        'email' => $usuario->getEmail(), 
                        'nombre' => $usuario->getFirstname(), 
                        'apellido' => $usuario->getLastname()
                    ));
                    $documento1 = __DIR__.'/../../public/themes/'
                                    . $this->getParameter('app.theme')
                                    . '/images/'. $this->getParameter('app.logo_correo_archivo');

                    $message = (new \Swift_Message('Sala eTAB'))
                        //->attach(\Swift_Attachment::fromPath($documento1))
                        ->setFrom($this->getParameter('app.from_correo'))
                        ->setTo($usuario->getEmail()) 
                        ->setBody($this->renderView('Page/sala.html.twig', array('dato' => $dato,'array' => $array)),"text/html");
                     $mailer->send($message);
                    $msg.="se envió correo a: ".$name." (".$usuario->getEmail().")\n\n";
                }
            }
            $em->flush();
        }
        $token  = md5(time());
        if($req->get("usuarios_sin_cuenta")!="")
        {
            
            $dato = array(array
            (
                'token' =>$token, 
                'sala' => $sala->getId(),
                'nombre_sala' => $sala->getNombre()
            ));
            
            
            $usuario = explode(",", preg_replace('/\s+/', '', $req->get("usuarios_sin_cuenta")));

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

                    $documento1 = __DIR__.'/../../public/themes/'
                        . $this->getParameter('app.theme')
                        . '/images/'. $this->getParameter('app.logo_correo_archivo');

                    $data = file_get_contents($documento1);
                    $tipo = pathinfo($documento1, PATHINFO_EXTENSION);
                    $dato['imagenbase64'] = 'data:image/' . $tipo . ';base64,' . base64_encode($data);

                    $message = (new \Swift_Message('Sala eTAB'))
                        //->attach(\Swift_Attachment::fromPath($documento1))
                        ->setFrom($this->getParameter('app.from_correo'))
                        ->setTo(trim($usuario[$i]))
                        ->setBody($this->renderView('Page/sala.html.twig', array('dato' => $dato,'array' => $array)),"text/html");
                    $mailer->send($message);
                    $msg.="se envio correo a: ".$usuario[$i]."\n\n";

                }
            }   
        }

        if($msg!="")
        {
            $social = new Social();
            $ahora = new \DateTime("now");
            
            $social->setToken($token);
            $social->setCreado($ahora);
            $social->setSala($sala);
            $social->setTiempoDias($datos->tiempo_dias);
            $social->setEsPermanente($datos->es_permanente);
            
            $em->persist($social);
            $em->flush();
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

    /**
     * @Route("/html_pdf", name="html_pdf", methods={"GET"})
     * 
     * @SWG\Get(
     *      tags={"Tablero Social"},
     *      summary="Exportar a PDF",
     *      description="Exporta a pdf el contenido de un html",
     *      produces={"application/json"},
     *      @SWG\Parameter(parameter="b_in_path", name="html", required=true, type="string", in="path"),
     *      @SWG\Parameter(parameter="h_in_path", name="header", required=true, type="string", in="path"),
     *      @SWG\Parameter(parameter="f_in_path", name="footer", required=true, type="string", in="path")
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="PDF"     
     *  ),
     */
    public function setHTML(Request $request, \jonasarts\Bundle\TCPDFBundle\TCPDF\TCPDF $pdf){ 
        $datos = $request; 

        $contenido  = str_replace("id=", "class=", urldecode($datos->get("html")));
        $header     = str_replace("id=", "class=", urldecode($datos->get("header")));
        $footer     = str_replace("id=", "class=", urldecode($datos->get("footer")));

        
        $public_path = $this->getParameter('kernel.project_dir') . '/public';
        
        $style = file_get_contents("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css");        
        $html  = '<html>
                    <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                    <style>                                                            
                        '.$style.'
                    </style>        
                    </head>
                    <body>                                      
                        '.$contenido.'                        
                    </body>
                </html>';
        
        //$pdf = $this->container->get('tcpdf')->create(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Checherman');
        $pdf->SetTitle(('eTAB ISECH'));
        $pdf->SetSubject('Eliecer Ramirez Esquinca');
        $pdf->SetKeywords('eTAB, PDF, ISECH, checherman, sm2015');
        

        $pdf->SetHeaderData($public_path.'/images/logo_salud_.png', '180');
        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
                
        // set font
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->AddPage();
        
        $filename = $datos->get("nombre");
        
        $pdf->writeHTML($html, true, false, true, false, '');          
        $pdf->Output($filename.".pdf",'I');

    }
}
