<?php

namespace App\Form;

use App\Entity\Course;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'course_table.columns.title',
                'required' => true,
            ])
            ->add('description', CKEditorType::class, [
                'label' => 'course_table.columns.description',
                'required' => true,
            ])
            ->add('level', ChoiceType::class, [
                'label' => 'course_table.columns.level',
                'required' => true,
                'choices' => array_flip(Course::getLevels()),
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
            'data_class' => Course::class,
        ]);
    }
}
