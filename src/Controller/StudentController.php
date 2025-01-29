<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
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
 * @Route("/student")
 */
class StudentController extends AbstractController
{
    /**
     * @Route("/list", name="students_list")
     */
    public function studentsListAction(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->setName('students_table')
            ->add('name', TextColumn::class, ['field' => 's.name', 'searchable' => true])
            ->add('surname', TextColumn::class, ['field' => 's.surname', 'searchable' => true])
            ->add('birth_date', DateTimeColumn::class, ['field' => 's.birth_date', 'format' => 'd.m.Y'])
            ->add('active', TwigColumn::class, ['template' => 'school/student/_partials/active_field.html.twig', 'searchable' => false])
            ->add('actions', TwigColumn::class, ['template' => 'school/student/_partials/actions_field.html.twig'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Student::class,
                'query' => function (QueryBuilder $builder) {
                    $builder
                        ->select('s')
                        ->from(Student::class, 's')
                        ->orderBy('s.surname', 'ASC');
                },
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('school/student/list.html.twig', [
            'datatable' => $table,
        ]);
    }

    /**
     * @Route("/edit/{student}", name="student_edit")
     */
    public function studentEditAction(Request $request, Student $student, ManagerRegistry $doctrine): Response
    {
        $manager = $doctrine->getManager();
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Student $student */
            $student = $form->getData();

            try {
                $manager->flush();
                $this->addFlash('success', 'Zmiany zostały zapisane');

                return $this->redirectToRoute('student_edit', ['student' => $student->getIdStudent()]);
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        }

        return $this->render('school/student/edit.html.twig', [
            'form' => $form->createView(),
            'student' => $student,
        ]);
    }

    /**
     * @Route("/add", name="student_add")
     */
    public function studentAddAction(Request $request, ManagerRegistry $doctrine): Response
    {
        $manager = $doctrine->getManager();
        $form = $this->createForm(StudentType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Student $student */
            $student = $form->getData();
            try {
                $manager->persist($student);

                $manager->flush();
                $this->addFlash('success', 'Uczeń został dodany');

                return $this->redirectToRoute('students_list');
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        }

        return $this->render('school/student/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{student}", name="student_delete")
     */
    public function studentDeleteAction(Request $request, Student $student, ManagerRegistry $doctrine): Response
    {
        if ($request->get('confirm')) {
            try {
                $doctrine->getManager()->remove($student);
                $doctrine->getManager()->flush();
                $this->addFlash('success', 'Uczeń został usunięty');

                return $this->redirectToRoute('students_list');
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        }

        return $this->render('school/student/delete.html.twig', [
            'student' => $student,
        ]);
    }

    /**
     * @Route("/edit_status/{student}", name="student_edit_status")
     */
    public function studentEditStatusAction(Student $student, ManagerRegistry $doctrine): Response
    {
        try {
            if ($student->isActive()) {
                $student->setActive(false);
            } else {
                $student->setActive(true);
            }
            $doctrine->getManager()->flush();
            $this->addFlash('success', 'Status ucznia został zmieniony');
        } catch (\Exception $exc) {
            $this->addFlash('error', $exc->getMessage());
        }

        return $this->redirectToRoute('students_list');
    }
}
