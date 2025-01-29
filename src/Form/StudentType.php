<?php

namespace App\Form;

use App\Entity\Student;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'students_table.columns.name',
                'required' => true,
            ])
            ->add('surname', TextType::class, [
                'label' => 'students_table.columns.surname',
                'required' => true,
            ])
            ->add('birthDate', DateType::class, [
                'label' => 'Data urodzenia',
                'required' => true,
                'html5' => true,
                'widget' => 'single_text',
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'Aktywny',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'show_legend' => false,
            'data_class' => Student::class,
        ]);
    }
}
