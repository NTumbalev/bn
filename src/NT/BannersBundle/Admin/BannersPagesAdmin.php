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
 * Banners Pages admin
 *
 * @package NTBannersBundle
 * @author  Nikolay Tumbalev <ntumbalev@gmail.com>
 */
class BannersPagesAdmin extends Admin
{
    /**
     * @inheritdoc
     */
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'rank',
    );

    /**
     * Configure the list
     *
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $list list
     */
    protected function configureListFields(ListMapper $list)
    {
        $request = $this->getRequest();
        $list
            ->addIdentifier('id', null, array('label' => 'form.id'))
            ->addIdentifier('banner', null, array('label' => 'form.banner'))
            ->addIdentifier('page', null, array('label' => 'form.page'))
            ->addIdentifier('banner.location', null, array('label' => 'form.bannerLocation'))
            ->add('position', null, array('label' => 'form.position'))
            ->add('isMain', null, array('label' => 'form.isMain'))
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
        $collection->add('order', 'order');
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
                ->add('id')
            ->end()
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', array(
                'class' => 'col-md-12',
                'label' => 'form.general',
                'translation_domain' => 'NTBannersBundle'
            ))
                ->add('position', 'choice', array(
                    'required' => true, 
                    'label' => 'form.position',
                    'choices' => \NT\BannersBundle\Entity\BannersPages::$positions
                ))
                ->add('banner', null, array(
                    'required' => true, 
                    'label' => 'form.banner'
                ))
                ->add('page', 'nt_tree', array(
                    'required' => false, 
                    'label' => 'form.page',
                    'class' => 'NT\CompaniesBundle\Entity\CompanyCategory',
                    'orderFields' => array('root', 'lft'),
                    'treeLevelField' => 'lvl',
                    'add_empty' => $this->trans('select.page'),
                    'max_level' => 0
                ))
                ->add('isMain', null, array(
                    'label' => 'form.isMain',
                    'required' => false
                ))
                ->add('onAllCategories', null, array(
                    'label' => 'На всички категории',
                    'required' => false
                ))
            ->end()
        ->end();
    }
}
