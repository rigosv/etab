<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use App\Entity\Boletin;


class BoletinAdmin extends Admin
{
	protected $datagridValues = array(
        '_page' => 1, // Display the first page (default = 1)
        '_sort_order' => 'ASC', // Descendant ordering (default = 'ASC')
        '_sort_by' => 'nombre' // name of the ordered field (default = the model id field, if any)
    );	

	public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('nombre')
            ->add('sala')
            ->add('grupo')
			->add('creado')
			->add('actualizado')
        ;
    }
	public function configureFormFields(FormMapper $formMapper) 
	{
        $formMapper
			
            ->add('nombre', null, array('label' => $this->getTranslator()->trans('nombre'), 'attr'=> array('class'=>'form-control')))        
            ->add('sala', null, array('label' => $this->getTranslator()->trans('sala'), 'required' => true))
            ->add('token', HiddenType::class, array('label' => $this->getTranslator()->trans('token')))
            ->add('grupo', null, array('label' => $this->getTranslator()->trans('grupo'), 'required' => true))				
            ->add('enviar', CheckboxType::class, array(
                'label' => $this->getTranslator()->trans('enviar'), 
                'required' => false                
                )
            );

            $formMapper
            ->setHelps(array(
                'enviar' => ('marque_enviar')
            ));
            
    }

    
	public function configureDatagridFilters(DatagridMapper $datagridMapper) 
	{
        $datagridMapper
                ->add('nombre', null, array('label' => $this->getTranslator()->trans('nombre')))
        ;
    }

    public function configureListFields(ListMapper $listMapper) 
	{
        $listMapper
                ->addIdentifier('nombre', null, array('label' => $this->getTranslator()->trans('nombre')))
				->add('sala', null, array('label' => $this->getTranslator()->trans('sala')))
				->add('grupo', null, array('label' => $this->getTranslator()->trans('grupo')))
                ->add('creado', null, array('label' => $this->getTranslator()->trans('creado')))
				->add('actualizado', null, array('label' => $this->getTranslator()->trans('actualizado')))
        ;
    }

	public function getBatchActions()
    {
        $actions = parent::getBatchActions();
        unset($actions['delete']);
        return $actions;
    }

	
}
?>