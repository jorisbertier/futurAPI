<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class NftSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->setMethod('GET')
        ->add('nftTitle', TextType::class, [
            'label' => 'Titre du NFT :',
            'required' => false
        ])
        // ->add('userEmail', TextType::class, [
        //     'label' => 'Email du créateur',
        //     'required' => false
        // ]);
        ->add('dateCreation', DateTimeType::class, [
            'date_widget' => 'choice',
            'label' => 'Par date de création supérieure à :',
            'required' => false
        ])
        ->add('orderByPrice', ChoiceType::class, [
            'label' => 'Par prix :',
            'required' => false,
            'choices'  => [
                'Décroissant' => 'DESC',
                'Croissant' => 'ASC',
            ],
        ]);
    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
