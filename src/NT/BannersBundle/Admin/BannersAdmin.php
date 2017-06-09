<?php
/**
 * This file is part of the NTBannersBundle.
 *
 * (c) Nikolay Tumbalev <ntumbalev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NT\BannersBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Admin\Admin;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Validator\ErrorElement;

/**
 * Admin class for Banners
 *
 * @package NTBannersBundle
 * @author  Nikolay Tumbalev <ntumbalev@gmail.com>
 */
class BannersAdmin extends Admin
{
    /**
     * Configure the list
     *
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $list list
     */
    protected function configureListFields(ListMapper $list)
    {
        $request = $this->getRequest();
        $list
            ->addIdentifier('title', null, array('label' => 'form.title'))
            ->add('publishWorkflow.isActive', null, array('label' => 'form.isActive', 'editable' => true))
            ->add('_action', 'actions', array(
                    'actions' => array(
                        'edit' => array(),
                        'delete' => array(),
                        #'history' => array('template' => 'NTCoreBundle:Admin:list_action_history.html.twig'),
                    ), 'label' => 'link_actions',
                ))
            ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
                ->add('title')
            ->end()
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $imageAdmin = $this->configurationPool->getAdminByClass("Application\\Sonata\\MediaBundle\\Entity\\Media");
        $translationAdmin = $this->configurationPool->getAdminByAdminCode('nt.admin.banners.translation');
        $ffds = $translationAdmin->getFormFieldDescriptions();

        $formMapper
            ->with('General', array(
                'class' => 'col-md-12',
                'label' => 'form.general',
                'translation_domain' => 'NTBannersBundle'
            ))
                ->add('title', null, array(
                    'label' => 'form.title'
                ))
                ->add('translations', 'a2lix_translations', array(
                    'fields' => array(
                        'image' => array(
                            'label' => 'Снимка',
                            'required' => false,
                            'field_type' => 'sonata_type_model_list',
                            'model_manager' => $this->getModelManager(),
                            'sonata_field_description' => $ffds['image'],
                            'class' => $imageAdmin->getClass(),
                            'translation_domain' => 'NTBannersBundle',
                        )
                    ),
                    'label' => 'form.translations',
                    'translation_domain' => 'NTBannersBundle'
                ))
                ->end()
            ->end()
            ->with('Publish Workflow', array('tab' => true))
                ->with('Publish Workflow', array(
                    'class' => 'col-md-12',
                    'label' => 'form.general',
                    'translation_domain' => 'NTBannersBundle',
                ))
                    ->add('publishWorkflow', 'nt_publish_workflow', array(
                        'is_active' => $this->getSubject()->getPublishWorkflow() ? $this->getSubject()->getPublishWorkflow()->getIsActive() : true,
                    ))
                ->end()
            ->end();
    }
}
