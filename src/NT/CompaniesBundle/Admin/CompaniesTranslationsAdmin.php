<?php

namespace NT\CompaniesBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class CompaniesTranslationsAdmin extends Admin
{

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('image', 'sonata_type_model_list', array(
                'label' => 'form.image',
                'translation_domain' => 'NTCompaniesBundle'
            ), array(
                'link_parameters' => array(
                    'context' => 'nt_companies_image'
                ))
            )
            ->add('gallery', 'sonata_type_model_list', array(
                'label' => 'form.gallery',
                'translation_domain' => 'NTCompaniesBundle'
            ), array(
                'link_parameters' => array(
                    'context' => 'nt_companies_gallery'
                ))
            )
        ->end();
    }
}
