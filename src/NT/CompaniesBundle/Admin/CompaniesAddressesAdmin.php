<?php
/**
 * This file is part of the NTCompaniesBundle.
 *
 * (c) Nikolay Tumbalev <ntumbalev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NT\CompaniesBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

/**
 *  Admin class for CompaniesAddresses
 *
 * @package NTCompaniesBundle
 * @author Nikolay Tumbalev <ntumbalev@gmail.com>
 */
class CompaniesAddressesAdmin extends Admin
{
    /**
     * @inheritdoc
     */
    protected $datagridValues = array(
         '_page' => 1,
         '_sort_order' => 'ASC',
     );

    /**
     * {@inheritdoc}
     */
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        $actions['hide'] = [
            'label'            => $this->trans('action_hide', array(), 'NTCoreBundle'),
            'ask_confirmation' => true, // If true, a confirmation will be asked before performing the action
        ];
        $actions['show'] = [
            'label'            => $this->trans('action_show', array(), 'NTCoreBundle'),
            'ask_confirmation' => true, // If true, a confirmation will be asked before performing the action
        ];

        return $actions;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        parent::configureTabMenu($menu, $action, $childAdmin);

        if ($action == 'history') {
            $id = $this->getRequest()->get('id');
            $menu->addChild(
                "General",
                array('uri' => $this->generateUrl('history', array('id' => $id)))
            );

            $locales = $this->getConfigurationPool()->getContainer()->getParameter('locales');

            foreach ($locales as $value) {
                $menu->addChild(
                    strtoupper($value),
                    array('uri' => $this->generateUrl('history', array('id' => $id, 'locale' => $value)))
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
                ->add('title')
                ->add('created_at')
                ->add('updated_at')
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        // $collection->add('history', $this->getRouterIdParameter().'/history');
        // $collection->add('history_view_revision', $this->getRouterIdParameter().'/preview/{revision}');
        // $collection->add('history_revert_to_revision', $this->getRouterIdParameter().'/revert/{revision}');
        $collection->add('order', 'order');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', null, array('label' => 'form.id'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id', null, array('label' => 'list.id'))
            ->add('publishWorkflow.isActive', null, array('label' => 'list.isActive', 'editable' => true))
            ->add('_action', 'actions', array(
                    'actions' => array(
                        'edit' => array(),
                        'delete' => array(),
                        // 'history' => array('template' => 'NTCoreBundle:Admin:list_action_history.html.twig'),
                    ), 'label' => 'actions',
                ))
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('tab.general', array('tab' => true))
                ->add('translations', 'a2lix_translations', array(
                    'fields' => array(
                        'address' => array(
                            'field_type' => 'textarea',
                            'label' => 'form.address',
                            'required' => false,
                            // 'attr' => array(
                            //     'class' => 'tinymce',
                            //     'data-theme' => 'bbcode'
                            // )
                        )
                    ),
                    'translation_domain' => 'NTCompaniesBundle',
                    'label' => 'form.translations',
                ))
                ->add('latitude', null, array(
                    'required' => false,
                    'label' => 'form.latitude'
                ))
                ->add('longitude', null, array(
                    'required' => false,
                    'label' => 'form.longitude'
                ))
                ->add('phone', null, array(
                    'required' => false,
                    'label' => 'form.phone'
                ))
                ->end()
            ->end()
            ->with('tab.publish_workflow', array('tab' => true))
                ->with('Publish Workflow', array('class' => 'col-md-12', 'label' => 'form.general', 'translation_domain' => 'NTAttributesBundle'))
                    ->add('publishWorkflow', 'nt_publish_workflow', array(
                        'is_active' => $this->getSubject() ? $this->getSubject()->getPublishWorkflow() ? $this->getSubject()->getPublishWorkflow()->getIsActive() : true : true,
                    ))
                ->end()
            ->end();
    }
}