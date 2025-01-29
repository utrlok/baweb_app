<?php

namespace App\Form;

use App\Entity\Student;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var User $user */
        $user = $options['user'];

        $builder
            ->add('name', TextType::class, [
                'label' => 'users_table.columns.name',
                'required' => true,
            ])
            ->add('surname', TextType::class, [
                'label' => 'users_table.columns.surname',
                'required' => true,
            ])
            ->add('email', TextType::class, [
                'label' => 'users_table.columns.email',
                'required' => true,
            ])
            ->add('student', EntityType::class, [
                'required' => false,
                'class' => Student::class,
                'query_builder' => function (EntityRepository $er) use ($user): QueryBuilder {
                    return $er->createQueryBuilder('s')
                        ->leftJoin(User::class, 'u', 'WITH', 'u.student = s')
                        ->where('u.idUser IS NULL OR u.idUser = :userId')
                        ->setParameter('userId', $user->getIdUser());

                },
                'choice_label' => 'fullName',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'show_legend' => false,
            'data_class' => User::class,
            'user' => null,
        ]);
    }
}
