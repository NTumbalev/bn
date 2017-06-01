<?php

namespace NT\CompaniesBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class CompanyCategoryTranslationsAdmin extends Admin
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
                    'context' => 'nt_company_category_image'
                ))
            )
        ->end();
    }
}
