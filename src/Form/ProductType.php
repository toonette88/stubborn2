<?php
namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;


class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Nom',
            ])
            ->add('description', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Description',
            ])
            ->add('price', NumberType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Prix',
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image du produit (JPG, PNG, GIF)',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => true,
            ])
            ->add('is_featured', CheckboxType::class, [
                'label' => 'Mise en avant',
                'required' => false,
            ])
            ->add('stockXS', NumberType::class, ['attr' => ['class' => 'form-control'],'label' => 'Stock XS'])
            ->add('stockS', NumberType::class, ['attr' => ['class' => 'form-control'],'label' => 'Stock S'])
            ->add('stockM', NumberType::class, ['attr' => ['class' => 'form-control'],'label' => 'Stock M'])
            ->add('stockL', NumberType::class, ['attr' => ['class' => 'form-control'],'label' => 'Stock L'])
            ->add('stockXL', NumberType::class, ['attr' => ['class' => 'form-control'],'label' => 'Stock XL']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
