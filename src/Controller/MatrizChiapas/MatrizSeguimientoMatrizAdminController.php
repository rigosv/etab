<?php

namespace App\Controller\MatrizChiapas;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use App\Entity\MatrizChiapas\MatrizSeguimientoMatriz;
use App\Entity\MatrizChiapas\MatrizIndicadoresDesempeno;

class MatrizSeguimientoMatrizAdminController extends Controller
{
    public function batchActionDelete(ProxyQueryInterface $selectedModelQuery, Request $request = null)
    { 
        $datos = (object) $request->request->all();
        $data = $datos->idx;
        
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        
            
        if($data){
           foreach ($data as $id) {
                // usuarios asignados                    
                $statement = $connection->prepare("DELETE  FROM matriz_indicadores_usuario WHERE  id_matriz = $id");
                $statement->execute();
                $usuarios = $statement->fetchAll();

                // desemepño                
                $statement = $connection->prepare("SELECT * FROM matriz_indicadores_desempeno WHERE  id_matriz = $id");
                $statement->execute();
                $desempenos = $statement->fetchAll();
                
                $indicadores_desempeno = [];
                foreach($desempenos as $desempeno){
                    // relaciones asignadas
                    $statement = $connection->prepare("SELECT * FROM matriz_indicadores_relacion WHERE  id_desempeno = '".$desempeno["id"]."'");
                    $statement->execute();
                    $relaciones = $statement->fetchAll();

                    foreach($relaciones as $relacion){
                        $statement = $connection->prepare("DELETE FROM matriz_indicadores_relacion_alertas WHERE  matriz_indicador_relacion_id = '".$relacion["id"]."'");
                        $statement->execute();
                        $alertas = $statement->fetchAll();
                    }
                    $statement = $connection->prepare("DELETE FROM matriz_indicadores_relacion WHERE  id_desempeno = '".$desempeno["id"]."'");
                    $statement->execute();
                    $relaciones = $statement->fetchAll();

                    // etab asignadas
                    $statement = $connection->prepare("SELECT * FROM matriz_indicadores_etab WHERE  id_desempeno = '".$desempeno["id"]."'");
                    $statement->execute();
                    $etab = $statement->fetchAll();

                    foreach($etab as $key => $item){                                                            
                        $statement = $connection->prepare("DELETE FROM matriz_indicadores_etab_alertas WHERE  matriz_indicador_etab_id = '".$item["id"]."'");
                        $statement->execute();
                        $alertas = $statement->fetchAll();                            
                    }

                    $statement = $connection->prepare("DELETE FROM matriz_indicadores_etab WHERE  id_desempeno = '".$desempeno["id"]."'");
                    $statement->execute();
                    $etab = $statement->fetchAll();

                    // datos
                    $statement = $connection->prepare("SELECT * FROM matriz_seguimiento WHERE  id_desempeno = '".$desempeno["id"]."'");
                    $statement->execute();
                    $etab = $statement->fetchAll();

                    foreach($etab as $key => $item){                                                            
                        $statement = $connection->prepare("DELETE FROM matriz_seguimiento_dato WHERE  id_matriz = '".$item["id"]."'");
                        $statement->execute();
                        $alertas = $statement->fetchAll();                            
                    }

                    $statement = $connection->prepare("DELETE FROM matriz_seguimiento WHERE  id_desempeno = '".$desempeno["id"]."'");
                    $statement->execute();
                    $etab = $statement->fetchAll();
                }                    
                $statement = $connection->prepare("DELETE FROM matriz_indicadores_desempeno WHERE  id_matriz = $id");
                $statement->execute();
                $desempenos = $statement->fetchAll();  
                

                $statement = $connection->prepare("DELETE  FROM matriz_seguimiento_matriz WHERE  id = $id");
                $statement->execute();
                $data = $statement->fetchAll(); 
            }
            // ejecutar el contenido de la memoria
            $em->flush();
        }
        $this->addFlash('sonata_flash_success', 'flash_batch_delete_success');

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }
    
}
