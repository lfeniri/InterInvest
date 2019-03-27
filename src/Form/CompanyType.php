<?php

namespace App\Form;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use App\Entity\legalForm;
use App\Entity\Address;
use App\Form\AddressType;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('name')
            ->add('sirenNumber')
            ->add('cityOfRegistration')
            ->add('registrationDate')
            ->add('capital')
            ->add('legalForm',EntityType::class,[
                'class' => LegalForm::class,
                'choice_label' => 'name'
            ])
            ->add('addresses',CollectionType::class,[
                'entry_type' => AddressType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
