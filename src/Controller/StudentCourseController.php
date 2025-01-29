<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\StudentCourse;
use App\Form\StudentCourseType;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/student_course")
 */
class StudentCourseController extends AbstractController
{
    /**
     * @Route("/list/{student}", name="student_courses_list")
     */
    public function studentCoursesListAction(Request $request, Student $student, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->setName('student_course_table')
            ->add('title', TextColumn::class, ['field' => 'c.title', 'searchable' => true])
            ->add('start', DateTimeColumn::class, ['field' => 'sc.start', 'format' => 'd.m.Y H:i:s'])
            ->add('finish', DateTimeColumn::class, ['field' => 'sc.finish'])
            ->add('actions', TwigColumn::class, ['template' => 'school/student_course/_partials/actions_field.html.twig'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => StudentCourse::class,
                'query' => function (QueryBuilder $builder) use ($student) {
                    $builder
                        ->select('sc')
                        ->from(StudentCourse::class, 'sc')
                        ->addSelect('s')
                        ->addSelect('c')
                        ->join('sc.course', 'c')
                        ->join('sc.student', 's')
                        ->where('sc.student = :idStudent')
                        ->setParameter('idStudent', $student->getIdStudent());
                },
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('school/student_course/list.html.twig', [
            'student' => $student,
            'datatable' => $table,
        ]);
    }

    /**
     * @Route("/add/{student}", name="student_course_add")
     */
    public function studentCourseAddAction(Request $request, Student $student, ManagerRegistry $doctrine): Response
    {
        $manager = $doctrine->getManager();
        $form = $this->createForm(StudentCourseType::class, null, ['student' => $student]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var StudentCourse $studentCourse */
            $studentCourse = $form->getData();
            $studentCourse
                ->setStudent($student)
                ->setStart(new \DateTime());
            try {
                $manager->persist($studentCourse);

                $manager->flush();
                $this->addFlash('success', 'Kurs został przypisany');

                return $this->redirectToRoute('student_courses_list', ['student' => $student->getIdStudent()]);
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        }

        return $this->render('school/student_course/add.html.twig', [
            'form' => $form->createView(),
            'student' => $student,
        ]);
    }

    /**
     * @Route("/delete/{studentCourse}", name="student_course_delete")
     */
    public function studentCourseDeleteAction(Request $request, StudentCourse $studentCourse, ManagerRegistry $doctrine): Response
    {
        if ($request->get('confirm')) {
            try {
                $student = $studentCourse->getStudent();
                $doctrine->getManager()->remove($studentCourse);
                $doctrine->getManager()->flush();
                $this->addFlash('success', 'Dostęp do kursu został usunięty');

                return $this->redirectToRoute('student_courses_list', ['student' => $student->getIdStudent()]);
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        }

        return $this->render('school/student_course/delete.html.twig', [
            'studentCourse' => $studentCourse,
        ]);
    }

    /**
     * @Route("/stats/{studentCourse}", name="student_course_stats")
     */
    public function studentCourseEditStatsAction(StudentCourse $studentCourse, ManagerRegistry $doctrine): Response
    {
        return $this->render('school/student_course/stats.html.twig', [
            'studentCourse' => $studentCourse,
        ]);
    }
}