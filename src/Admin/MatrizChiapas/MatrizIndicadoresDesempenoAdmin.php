<?php

namespace App\Admin\MatrizChiapas;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use App\Entity\MatrizChiapas\MatrizIndicadoresDesempeno;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\CoreBundle\Form\Type\CollectionType;

use App\Entity\FichaTecnica;

class MatrizIndicadoresDesempenoAdmin extends Admin
{   
    private $repository;
	protected $datagridValues = array(
        '_page' => 1, // Display the first page (default = 1)
        '_sort_order' => 'ASC', // Descendant ordering (default = 'ASC')
        '_sort_by' => 'nombre' // name of the ordered field (default = the model id field, if any)
    );	
    public function setRepository($repository)
    {
        $this->repository = $repository;
    }
	public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('matriz', null, array('label' => '_matriz_', 'required' => true))
            ->add('nombre') 
            ->add('orden') 
            ->add('matrizIndicadoresEtab', null, array('label' => '_indicador_etab_',
                    'expanded' => true,
                    'class' => FichaTecnica::class,
                    'query_builder' => function ($repository) {
                        return $repository->createQueryBuilder('i')->addOrderBy('nombre','ASC');
                    }))
                ->add('indicators', CollectionType::class, 
                array(              
                    'label' => '_indicador_rel_',
                    'required' => true
                ), 
                array(
                    'class'=>'form-control',
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'position'                                    
                ))           
			->add('creado')
			->add('actualizado')
        ;
    }
	public function configureFormFields(FormMapper $formMapper) 
	{
        $formMapper
                ->add('matriz', null, array('label' => '_matriz_'))
                ->add('nombre', null, array('label' => 'nombre'))
                ->add('orden', null, array('label' => 'orden'))
				->add('matrizIndicadoresEtab', null,  array('label' => '_indicador_etab_',
                    'expanded' => false,
                    'class' => FichaTecnica::class,
                    'query_builder' => function ($repository) {
                        return $repository->createQueryBuilder('i')->orderBy('i.nombre', 'ASC');
                    }))
                ->add('indicators', CollectionType::class, 
                array(              
                    'label' => '_indicador_rel_',
                    'required' => true
                ), 
                array(
                    'class'=>'form-control',
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'position'                                    
                ))
        ;
    }
	public function configureDatagridFilters(DatagridMapper $datagridMapper) 
	{
        $datagridMapper
                ->add('nombre', null, array('label' => 'nombre'))
                ->add('matriz', null, array('label' => '_matriz_'))
        ;
    }

    public function configureListFields(ListMapper $listMapper) 
	{
        $listMapper
                ->addIdentifier('matriz', null, array('label' => '_matriz_'))
                ->addIdentifier('nombre', null, array('label' => 'nombre'))
                ->add('creado', null, array('label' => 'creado'))
				->add('actualizado', null, array('label' => 'actualizado'))
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
                return 'CRUD/matriz-edit.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }    
    
    public function getExportFields()
    {
    	return array('id', 'nombre', 'creado', 'actualizado');
    }

    public function prePersist($indicators)
    {
        $this->setIndicadors($indicators);
    }

    public function setIndicadors($MatrizIndicadoresDesempeno)
    {
        $indicators = $MatrizIndicadoresDesempeno->getIndicators();
        $MatrizIndicadoresDesempeno->removeIndicators();
        if ($indicators != null and count($indicators) > 0) {
            foreach ($indicators as $indicator) {
                $indicator->setDesempeno($MatrizIndicadoresDesempeno);
                $MatrizIndicadoresDesempeno->addIndicator($indicator);
            }
        }
    }

    public function preUpdate($indicators)
    {
        $this->setIndicadors($indicators);       
    }
}
