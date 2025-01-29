<?php

namespace App\Form;

use App\Entity\Lesson;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonType extends AbstractType
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
            ->add('active', CheckboxType::class, [
                'label' => 'Aktywna',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'show_legend' => false,
            'data_class' => Lesson::class,
        ]);
    }
}
