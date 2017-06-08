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
 *  Admin class for Companies
 *
 * @package NTCompaniesBundle
 * @author Nikolay Tumbalev <ntumbalev@gmail.com>
 */
class CompaniesAdmin extends Admin
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
            ->add('title', null, array('label' => 'form.title'))
            ->add('companyCategories', null, array('label' => 'Категории'));
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('title', null, array('label' => 'list.title'))
            ->add('companyCategories', null, array('label' => 'form.categories'))
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
        $mediaAdmin = $this->configurationPool->getAdminByClass("Application\\Sonata\\MediaBundle\\Entity\\Media");
        $galleryAdmin = $this->configurationPool->getAdminByClass("Application\\Sonata\\MediaBundle\\Entity\\Gallery");
        $translationAdmin = $this->configurationPool->getAdminByAdminCode('nt.companies.admin.companies_translations');
        $ffds = $translationAdmin->getFormFieldDescriptions();
        $ffds['image']->setAssociationAdmin($mediaAdmin);
        $ffds['gallery']->setAssociationAdmin($galleryAdmin);

        $formMapper
            ->with('tab.general', array('tab' => true))
                ->add('companyCategories', 'sonata_type_model', array(
                    'label' => 'form.categories',
                    'required' => false,
                    'multiple' => true,
                    'btn_add' => false
                ))
                ->add('location', null, array(
                    'required' => false,
                    'label' => 'form.location'
                ))
                ->add('translations', 'a2lix_translations', array(
                    'fields' => array(
                        'slug' => array(
                            'field_type' => 'text',
                            'label' => 'form.slug',
                            'required' => false,
                            'constraints' => array(
                                new \Symfony\Component\Validator\Constraints\Regex(
                                    array(
                                        'pattern' => '/[a-zA-Zа-яА-Я]/',
                                        'match' => true,
                                        'message' => 'Полето слъг трябва да съдържа буква!'
                                    )
                                )
                            )
                        ),
                        'title' => array(
                            'field_type' => 'text',
                            'label' => 'form.title'
                        ),
                        'description' => array(
                            'field_type' => 'textarea',
                            'label' => 'form.description',
                            'required' => false,
                            'attr' => array(
                                'class' => 'tinymce',
                                'data-theme' => 'bbcode'
                            )
                        ),
                        'image' => array(
                            'label' => 'Изображение (Препоръчителен минимален размер 282px x 211px)',
                            'required' => false,
                            'field_type' => 'sonata_type_model_list',
                            'model_manager' => $this->getModelManager(),
                            'sonata_field_description' => $ffds['image'],
                            'class' => $mediaAdmin->getClass(),
                            'sonata_admin' => $mediaAdmin->getClass(),
                            'translation_domain' => 'NTCompaniesBundle',
                        ),
                        'gallery' => array(
                            'label' => 'form.gallery',
                            'required' => false,
                            'field_type' => 'sonata_type_model_list',
                            // 'context' => 'nt_companies_gallery',
                            // 'field_type' => 'nt_gallery_type',
                            // 'video' => false,
                            'model_manager' => $this->getModelManager(),
                            'sonata_field_description' => $ffds['gallery'],
                            'class' => $galleryAdmin->getClass(),
                            'translation_domain' => 'NTCompaniesBundle',
                        ),
                    ),
                    'translation_domain' => 'NTCompaniesBundle',
                    'label' => 'form.translations',
                ))
                ->add('addresses', 'sonata_type_collection', array(
                    'required' => false,
                    'label' => 'form.addresses',
                    'cascade_validation' => true,
                    'sonata_admin' => 'nt.companies.admin.companies_addresses'
                ), array(
                    'edit'       => 'inline',
                    'inline'     => 'table',
                    'admin_code' => 'nt.companies.admin.companies_addresses',
                    
                ))
                ->add('isTop', null, array(
                    'required' => false,
                    'label' => 'form.isTop'
                ))
                ->end()
            ->end()
            ->with('SEO', array('tab' => true))
                ->with('SEO', array('collapsed' => true, 'class' => 'col-md-12'))
                    ->add('metaData', 'meta_data')
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


    public function validate(\Sonata\AdminBundle\Validator\ErrorElement $errorElement, $object)
    {
        foreach ($object->getTranslations() as $key => $translation) {
            $checkSlug = !empty($translation->getSlug()) ? $translation->getSlug() : $translation->getTitle();
            if (!preg_match('/[a-zA-Zа-яА-Я]/', $checkSlug)) {
                $errorElement->addViolation('Полето слъг трябва да съдържа буква!');
            }
        }
    }
}