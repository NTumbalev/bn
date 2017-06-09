<?php
namespace NT\CompaniesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Email;

class PublishCompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('POST')
            ->add('name', 'text', array(
                'label' => 'contact.name',
                'translation_domain' => 'NTFrontendBundle',
                'required' => true,
                'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'required-entry form-control',
                    'placeholder' => 'contact.name'
                ),
                'label_attr' => array(
                    'class' => 'col-md-3 control-label'
                )
            ))
            ->add('family', 'text', array(
                'label' => 'contact.family',
                'translation_domain' => 'NTFrontendBundle',
                'required' => true,
                'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'required-entry form-control',
                    'placeholder' => 'contact.family'
                ),
                'label_attr' => array(
                    'class' => 'col-md-3 control-label'
                )
            ))
            ->add('phone', 'text', array(
                'label' => 'contact.phone',
                'translation_domain' => 'NTFrontendBundle',
                'required' => true,
                'constraints' => array(
                    new Regex(array(
                        'pattern' => '/^(0|\+)[0-9]+$/',
                        'match' => true,
                        'message' => 'only_numbers',
                    )),
                    new NotBlank(array('message' => 'required_field')),
                ),
                'attr' => array(
                    'class' => 'validation-phone form-control',
                    'placeholder' => 'contact.phone'
                ),
                'label_attr' => array(
                    'class' => 'col-md-3 control-label'
                )
            ))
            ->add('email', 'text', array(
                'label' => 'contact.email',
                'translation_domain' => 'NTFrontendBundle',
                'required' => true,
                'constraints' => array(
                    new NotBlank(array('message' => 'required_field')),
                    new Email()
                ),
                'attr' => array(
                    'class' => 'required-entry validation-email form-control',
                    'placeholder' => 'contact.email'
                ),
                'label_attr' => array(
                    'class' => 'col-md-3 control-label'
                )
            ))
            ->add('address', 'text', array(
                'label' => 'contact.address',
                'translation_domain' => 'NTFrontendBundle',
                'required' => false,
                // 'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'required-entry form-control',
                    'placeholder' => 'contact.address'
                ),
                'label_attr' => array(
                    'class' => 'col-md-3 control-label'
                )
            ))
            ->add('city', 'text', array(
                'label' => 'contact.city',
                'translation_domain' => 'NTFrontendBundle',
                'required' => false,
                // 'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'contact.city'
                ),
                'label_attr' => array(
                    'class' => 'col-md-3 control-label'
                )
            ))
            ->add('message', 'textarea', array(
                'label' => 'contact.message',
                'translation_domain' => 'NTFrontendBundle',
                'required' => true,
                'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'required-entry form-control',
                    'placeholder' => 'contact.message',
                    'cols' => 6,
                    'rows' => 5
                ),
                'label_attr' => array(
                    'class' => 'col-md-3 control-label'
                )
            ))
            // ->add('captcha', 'ds_re_captcha', array('mapped' => false))
            ;
    }

    public function getName()
    {
        return 'publish_company';
    }
}
