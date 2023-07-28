<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\CollectionNft;
use App\Entity\Nft;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class NftType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filePath', FileType::class, [
                'mapped' => false
            ])
            ->add('dateCreation')
            ->add('dateDrop')
            ->add('price')
            ->add('title')
            ->add('description')
            ->add('alt')
            ->add('user')
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'label',
                'multiple' => true,
            ])
            ->add('collection', EntityType::class, [
                'class' => CollectionNft::class,
                'choice_label' => 'title',
                'multiple' => true,
            ])
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
