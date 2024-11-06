<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class SelectFriendsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('friends', EntityType::class, [
            'class' => User::class,
            'label' => 'Sélectionnez les amis à qui partager le fichier',
            'choices' => $options['friends'],
            'attr' => ['class'=> 'form-control'], 'label_attr' => ['class'=> 'fw-bold'],
            'choice_label' => function($friend) {
                return $friend->getSurname() . ' ' . $friend->getName();
            },
            'expanded' => true,
            'multiple' => true,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('f')
                ->orderBy('f.surname', 'ASC')
                ->addOrderBy('f.name', 'ASC');
            },])
        ->add('partager', SubmitType::class, ['attr' => ['class'=> 'btn bg-primary text-white m-4' ],'row_attr' => ['class' => 'text-center'],])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'friends' => []
        ]);
    }
}
