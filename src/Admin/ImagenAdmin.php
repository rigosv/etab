<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ImagenAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1, // Display the first page (default = 1)
        '_sort_order' => 'ASC', // Descendant ordering (default = 'ASC')
        '_sort_by' => 'titulo' // name of the ordered field (default = the model id field, if any)
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('titulo', null, array('label'=> ('_titulo_')))
            ->add('descripcion', null, array('label'=> ('_descripcion_')))
            ->add('sala')
            ->add('archivo', FileType::class, array('required'=>false))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('titulo', null, array('label'=> ('_titulo_')))
            ->add('descripcion',null, array('label'=> ('_descripcion_')))
            ->add('sala')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('titulo', null, array('label'=> ('_titulo_')))
            ->add('descripcion', null, array('label'=> ('_descripcion_')))
            ->add('sala')
            ->add('archivo', 'file', array('label'=> ('_archivo_'),
                                            'template' => 'CRUD/list_image.html.twig'))                
        ;
    }

    public function getBatchActions()
    {
        $actions = parent::getBatchActions();
        unset($actions['delete']);
        return $actions;
    }
    
    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                return 'CRUD/imagen-edit.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }
    
    public function prePersist($salaAcciones){        
        $usuario = $this->getConfigurationPool()
                ->getContainer()
                ->get('security.token_storage')
                ->getToken()
                ->getUser();
        
        $salaAcciones->setUsuario($usuario);
    }
    
     /**
     * Cambiar la forma en que muestra el listado de imágenes de sala,
     * si es un usuario normal solo le muestra las imágenes que ha ingresado
     * @return \Sonata\AdminBundle\Datagrid\ProxyQueryInterface
     */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $usuario = $this->getConfigurationPool()
                ->getContainer()
                ->get('security.token_storage')
                ->getToken()
                ->getUser();
        if ($usuario->hasRole('ROLE_SUPER_ADMIN')) {
                return new ProxyQuery($query->where('1=1'));
        } else {
            return new ProxyQuery(
                    $query->where($query->getRootAlias() . '.usuario = '.$usuario->getId())
            );
        }
    }
}
