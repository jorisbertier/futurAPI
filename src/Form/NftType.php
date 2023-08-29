<?php

namespace App\Form;

use App\Entity\Nft;
use App\Entity\Category;
use App\Entity\CollectionNft;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class NftType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filePath', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Valid(),
                    new File([
                        'maxSize' => '8000k',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Format image',
                    ])
                ],
            ])
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
                'choice_label' => 'label',
                'multiple' => false,
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Nft::class,
        ]);
    }
}
