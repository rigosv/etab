<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use App\Entity\Social;
use App\Entity\GrupoIndicadores;
use App\Entity\FichaTecnica;

use App\Entity\Boletin;
use App\Admin\BoletinAdmin;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class TokenController extends Controller
{	
    /**
    * @Route("/publico/boletin/{sala}/{token}", name="boletin_publico", options={"expose"=true})
    */
	public function tokenAction($sala,$token)
	{
		$em = $this->getDoctrine()->getManager();
		$sa = $em->getRepository(Boletin::class)->getRuta($sala,$token);

		if (!$sa) 
			return $this->render('IndicadoresBundle:Page:error.html.twig', array(
				'error' => "No existe la sala: $sala",
				'bien' => ""));	
		else if($sa=="Error")	
			return $this->render('IndicadoresBundle:Page:error.html.twig', array(
				'error' => "El tiempo de este boletin ha expirado",
				'bien' => ""));
		else
		{	
            $sa['indicadores'] = $em->getRepository('IndicadoresBundle:GrupoIndicadores')
                    ->getIndicadoresSala($em->getRepository('IndicadoresBundle:GrupoIndicadores')->find($sa[0]['sala']));
        	$indicadores = $em->getRepository("IndicadoresBundle:FichaTecnica")->getIndicadoresPublicos();        
        	return $this->render('FichaTecnicaAdmin/tablero_public.html.twig', array(
                    'indicadores' => $indicadores,
        			'sala' => $sa
                ));
		}
		
    }
    /**
    * @Route("/publico/sala/{sala}/{token}", name="sala_publico", options={"expose"=true})
    */
    public function salaPublicoAction($sala,$token)
    {
        $em = $this->getDoctrine()->getManager();
        $sa = $em->getRepository(Social::class)->getRuta($sala,$token);

        if (!$sa) 
            return $this->render('Page/error.html.twig', array(
                'error' => "No existe la sala: $sala",
                'bien' => "")); 
        else if($sa=="Error")   
            return $this->render('Page/error.html.twig', array(
                'error' => "El tiempo de este boletin ha expirado",
                'bien' => ""));
        else
        {   
            $conn = $em->getConnection();
            
            $sql = "SELECT * FROM grupo_indicadores where id = $sala; ";
            
            $statement = $conn->prepare($sql);
            $statement->execute();
            $data = $statement->fetchAll();

            $data1 = []; 
            foreach ($data as $key => $value) {                    
                $sql = "SELECT * FROM grupo_indicadores_indicador
                where grupo_indicadores_id = ".$value["id"]." order by posicion asc; ";
                
                $statement = $conn->prepare($sql);
                $statement->execute();
                $value["indicadores"] = $statement->fetchAll();  
                array_push($data1, $value);                   
            } 

            return $this->render('FichaTecnicaAdmin/tablero_publico.html.twig', array('data' => json_encode($data1[0])));
        }
        
    }

	 /**
     * @Route("/iframe/{username}/{pass}", name="login_iframe", options={"expose"=true})
     */
    public function iframeAction($username, $pass)
    {
        $request = $this->container->get('request');
        // This data is most likely to be retrieven from the Request object (from Form)
        // But to make it easy to understand ...
        $_username = $username;
        $_password = $pass;

        // Retrieve the security encoder of symfony
        $factory = $this->get('security.encoder_factory');

        /// Start retrieve user
        // Let's retrieve the user by its username:
        // If you are using FOSUserBundle:
        $user_manager = $this->get('fos_user.user_manager');
        $user = $user_manager->findUserByUsername($_username);
        // Or by yourself
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)
                ->findOneBy(array('username' => $_username));
        /// End Retrieve user

        // Check if the user exists !
        if(!$user){
            return new Response(
                'Username doesnt exists',
                Response::HTTP_UNAUTHORIZED,
                array('Content-type' => 'application/json')
            );
        }

        /// Start verification
        $encoder = $factory->getEncoder($user);
        $salt = $user->getSalt();

        if(!$encoder->isPasswordValid($user->getPassword(), $_password, $salt)) {
            return new Response(
                'Username or Password not valid.',
                Response::HTTP_UNAUTHORIZED,
                array('Content-type' => 'application/json')
            );
        } 
        /// End Verification

        // The password matches ! then proceed to set the user in session
        
        //Handle getting or creating the user entity likely with a posted form
        // The third parameter "main" can change according to the name of your firewall in security.yml
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->get('security.token_storage')->setToken($token);

        // If the firewall name is not main, then the set value would be instead:
        // $this->get('session')->set('_security_XXXFIREWALLNAMEXXX', serialize($token));
        $this->get('session')->set('_security_main', serialize($token));
        
        // Fire the login event manually
        $event = new InteractiveLoginEvent($request, $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
        
     
        $refererUri = $this->container->get('router')->generate('_inicio');
		return new RedirectResponse($refererUri);
    }
}
?>