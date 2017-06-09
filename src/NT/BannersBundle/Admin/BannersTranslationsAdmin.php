<?php

namespace NT\BannersBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class BannersTranslationsAdmin extends Admin
{

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
        ->add('image', 'sonata_type_model_list', array(
            'label' => 'form.image',
            'translation_domain' => 'NTBannersBundle'
        ), array(
            'link_parameters' => array(
                'context' => 'nt_banners_images'
            ))
        )
        ->end();
    }
}