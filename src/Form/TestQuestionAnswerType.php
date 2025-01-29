<?php

namespace App\Form;

use App\Entity\TestQuestionAnswer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestQuestionAnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', TextType::class, [
                'label' => 'answer_table.columns.text',
                'required' => true,
            ])
            ->add('correct', CheckboxType::class, [
                'label' => 'Prawidłowa',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'show_legend' => false,
            'data_class' => TestQuestionAnswer::class,
        ]);
    }
}
