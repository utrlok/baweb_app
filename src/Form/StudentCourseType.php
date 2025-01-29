<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Student;
use App\Entity\StudentCourse;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentCourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Student $student */
        $student = $options['student'];

        $builder
            ->add('course', EntityType::class, [
                'label' => 'course.title',
                'required' => false,
                'class' => Course::class,
                'query_builder' => function (EntityRepository $er) use ($student): QueryBuilder {
                    return $er->createQueryBuilder('c')
                        ->leftJoin(StudentCourse::class, 'sc', 'WITH', 'sc.course = c')
                        ->where('sc.student != :idStudent OR sc.student IS NULL')
                        ->setParameter('idStudent', $student->getIdStudent());
                },
                'choice_label' => 'title',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'student' => null,
            'show_legend' => false,
            'data_class' => StudentCourse::class,
        ]);
    }
}
