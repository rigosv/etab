<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\AccesoExterno;
use App\Entity\User;

class AccesoExternoController extends AbstractController {

    /**
     * @Route("/ae/{token}/", name="sala_acceso_externo")
     */
    public function salaAccesoExterno($token, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $ahora = new \DateTime();

        $ae = $em->getRepository(AccesoExterno::class)->findOneBy(array('token' => $token));

        if ($ae == null and $ahora <= $ae->getCaducidad()) {
            throw $this->createNotFoundException($this->get('translator')->trans('_token_no_valido_'));
        }

        $this->accesoExterno('externo', 'externo', $request);
        
        return $this->redirectToRoute('admin_minsal_indicadores_fichatecnica_tablero', array('token' => $token));
        
    }

    /**
     * @Route("/externo/autenticar/{user}/{pw}", name="autenticar")
     */
    public function accesoExterno($user, $pw, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $usuarioBD = $em->getRepository(User::class)->findOneBy(array('username' => $user));

        // Get the encoder for the users password
        $encoder_service = $this->get('security.encoder_factory');
        $encoder = $encoder_service->getEncoder($usuarioBD);

        //Verificar si el password es correcto
        if ($encoder->isPasswordValid($usuarioBD->getPassword(), $pw, $usuarioBD->getSalt())) {
            $token = new UsernamePasswordToken($usuarioBD, $pw, $this->container->getParameter('fos_user.firewall_name'), $usuarioBD->getRoles());

            $this->get('security.token_storage')->setToken($token);

            $event = new InteractiveLoginEvent($request, $token);
            $this->get('event_dispatcher')->dispatch('security.interactive_login', $event);

        } else {
            throw $this->createNotFoundException($this->get('translator')->trans('_clave_no_valida_'));
        }
    }
    
    /**
     * @Route("/externo/autenticar/ppal/{user}/{pw}", name="autenticar")
     */
    public function accesoExternoAPrincipal($user, $pw, Request $request) {
        $this->accesoExterno($user, $pw, $request);
        
        return $this->redirectToRoute('_inicio');
    }
    
    /**
     * @Route("/admin/loginn", name="login_normal")
     */
    public function loginNormal() {
        
        return $this->redirectToRoute('_inicio');
    }

}
