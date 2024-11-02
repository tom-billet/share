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
use Symfony\Component\Form\Extension\Core\Type\FileType as TypeFileType;
use Symfony\Component\Validator\Constraints\File as ValidatorFile;

class FileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('file', TypeFileType::class, array('label' => 'Fichier', 'mapped'=>false,'attr' => ['class'=>'form-control'], 'label_attr' => ['class'=> 'fw-bold'],'constraints' => [
            new ValidatorFile([
                'maxSize' => '200k',
                'mimeTypes' => [
                'application/pdf',
                'application/x-pdf',
                'image/jpeg',
                'image/png',
                ],
            'mimeTypesMessage' => 'Le site accepte uniquement les fichiers PDF, PNG et JPG',
            ])
        ],))
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
