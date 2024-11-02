<?php

namespace App\Form;

use App\Entity\File;
use App\Entity\User;
use App\Entity\Subcategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('originalName', TextType::class, ['label' => 'Nom original','attr' => ['class'=> 'form-control'], 'label_attr' => ['class'=> 'fw-bold']])
            ->add('serverName', TextType::class, ['label' => 'Nom serveur','attr' => ['class'=> 'form-control'], 'label_attr' => ['class'=> 'fw-bold']])
            ->add('extension', TextType::class, ['label' => 'Extension','attr' => ['class'=> 'form-control'], 'label_attr' => ['class'=> 'fw-bold']])
            ->add('size', IntegerType::class, ['label' => 'Taille','attr' => ['class'=> 'form-control'], 'label_attr' => ['class'=> 'fw-bold']])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'label' => 'Utilisateur associé',
                'attr' => ['class'=> 'form-control'],
                'label_attr' => ['class'=> 'fw-bold'],
                'choice_label' => function($user) {
                    return $user->getSurname() . ' ' . $user->getName();
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                    ->orderBy('u.surname', 'ASC')
                    ->addOrderBy('u.name', 'ASC');
                },
            ])
            ->add('subcategories', EntityType::class, [
                'class' => Subcategory::class,
                'choices' => $options['subcategories'],
                'choice_label' => 'label',
                'expanded' => true,
                'multiple' => true,
                'label' => false, 'mapped' => false])
            ->add('ajouter', SubmitType::class, ['attr' => ['class' => 'btn bg-primary text-white m4'], 'row_attr' => ['class'=>'text-center'],])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => File::class,
            'subcategories' => []
        ]);
    }
}
