<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Subcategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Doctrine\ORM\EntityRepository;

class AddSubcategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, ['label' => 'Libellé','attr' => ['class'=> 'form-control'], 'label_attr' => ['class'=> 'fw-bold']])
            ->add('number', IntegerType::class, ['label' => 'Numéro','attr' => ['class'=> 'form-control'], 'label_attr' => ['class'=> 'fw-bold']])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'label' => 'Catégorie associée',
                'attr' => ['class'=> 'form-control'], 'label_attr' => ['class'=> 'fw-bold'],
                'choice_label' => function($category) {
                    return $category->getId() . ' ' . $category->getLabel();
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                    ->orderBy('c.id', 'ASC')
                    ->addOrderBy('c.label', 'ASC');
                },
            ])
            ->add('Ajouter', SubmitType::class, ['attr' => ['class' => 'btn bg-primary text-white m4'], 'row_attr' => ['class'=>'text-center'],])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subcategory::class,
        ]);
    }
}
