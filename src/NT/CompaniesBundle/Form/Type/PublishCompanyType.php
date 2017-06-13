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
            ->add('title', 'text', array(
                'label' => 'publish_company.title',
                'translation_domain' => 'NTFrontendBundle',
                'required' => true,
                'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'required-entry form-control',
                    'placeholder' => 'publish_company.title'
                ),
                'label_attr' => array(
                    'class' => 'col-md-12 control-label'
                )
            ))
            ->add('phone', 'text', array(
                'label' => 'publish_company.phone',
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
                    'placeholder' => 'publish_company.phone'
                ),
                'label_attr' => array(
                    'class' => 'col-md-12 control-label'
                )
            ))
            ->add('keywords', 'text', array(
                'label' => 'publish_company.keywords',
                'translation_domain' => 'NTFrontendBundle',
                'required' => false,
                // 'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'required-entry form-control',
                    'placeholder' => 'publish_company.keywords'
                ),
                'label_attr' => array(
                    'class' => 'col-md-12 control-label'
                ),
                'mapped' => false
            ))
            ->add('contactPerson', 'text', array(
                'label' => 'publish_company.contactPerson',
                'translation_domain' => 'NTFrontendBundle',
                'required' => true,
                'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'required-entry form-control',
                    'placeholder' => 'publish_company.contactPerson'
                ),
                'label_attr' => array(
                    'class' => 'col-md-12 control-label'
                )
            ))
            ->add('address', 'text', array(
                'label' => 'publish_company.address',
                'translation_domain' => 'NTFrontendBundle',
                'required' => true,
                'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'required-entry form-control',
                    'placeholder' => 'publish_company.address'
                ),
                'label_attr' => array(
                    'class' => 'col-md-12 control-label'
                ),
                'mapped' => false
            ))
            ->add('location', 'entity', array(
                'class' => 'NTLocationsBundle:Location',
                'label' => 'publish_company.location',
                'translation_domain' => 'NTFrontendBundle',
                'required' => true,
                'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'required-entry form-control selectpicker',
                    'placeholder' => 'publish_company.location'
                ),
                'label_attr' => array(
                    'class' => 'col-md-12 control-label'
                )
            ))
            ->add('webpage', 'text', array(
                'label' => 'publish_company.webpage',
                'translation_domain' => 'NTFrontendBundle',
                'required' => false,
                // 'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'required-entry form-control',
                    'placeholder' => 'publish_company.webpage'
                ),
                'label_attr' => array(
                    'class' => 'col-md-12 control-label'
                )
            ))
            ->add('email', 'text', array(
                'label' => 'publish_company.email',
                'translation_domain' => 'NTFrontendBundle',
                'required' => true,
                'constraints' => array(
                    new NotBlank(array('message' => 'required_field')),
                    new Email()
                ),
                'attr' => array(
                    'class' => 'required-entry validation-email form-control',
                    'placeholder' => 'publish_company.email'
                ),
                'label_attr' => array(
                    'class' => 'col-md-12 control-label'
                )
            ))
            ->add('companyCategories', 'entity', array(
                'class' => 'NTCompaniesBundle:CompanyCategory',
                'label' => 'publish_company.category',
                'translation_domain' => 'NTFrontendBundle',
                'required' => true,
                'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'form-control selectpicker',
                    'placeholder' => 'publish_company.category'
                ),
                'label_attr' => array(
                    'class' => 'col-md-12 control-label'
                )
            ))
            ->add('message', 'textarea', array(
                'label' => 'publish_company.message',
                'translation_domain' => 'NTFrontendBundle',
                'required' => false,
                // 'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'required-entry form-control',
                    'placeholder' => 'publish_company.message',
                    'cols' => 6,
                    'rows' => 5
                ),
                'label_attr' => array(
                    'class' => 'col-md-12 control-label'
                ),
                'mapped' => false
            ))
            // ->add('captcha', 'ds_re_captcha', array('mapped' => false))
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(\Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NT\CompaniesBundle\Entity\Company',
            'intention'  => 'publish_company',
            'translation_domain' => 'NTCompaniesBundle',
        ));
    }

    public function getName()
    {
        return 'publish_company';
    }
}
