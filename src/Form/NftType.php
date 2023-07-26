<?php

namespace App\Form;

use App\Entity\Nft;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NftType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateCreation')
            ->add('dateDrop')
            ->add('price')
            ->add('title')
            ->add('description')
            ->add('filePath')
            ->add('alt')
            ->add('user')
            ->add('categories')
            ->add('collection')
            ->add('eth')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Nft::class,
        ]);
    }
}
