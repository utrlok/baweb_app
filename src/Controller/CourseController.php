<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseType;
use App\Form\StudentType;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
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
 * @Route("/course")
 */
class CourseController extends AbstractController
{
    /**
     * @Route("/list", name="courses_list")
     */
    public function coursesListAction(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->setName('course_table')
            ->add('title', TextColumn::class, ['field' => 'c.title', 'searchable' => true])
            ->add('level', TwigColumn::class, ['template' => 'school/course/_partials/level_field.html.twig', 'searchable' => false])
            ->add('active', TwigColumn::class, ['template' => 'school/course/_partials/active_field.html.twig', 'searchable' => false])
            ->add('actions', TwigColumn::class, ['template' => 'school/course/_partials/actions_field.html.twig'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Course::class,
                'query' => function (QueryBuilder $builder) {
                    $builder
                        ->select('c')
                        ->from(Course::class, 'c')
                        ->orderBy('c.level', 'ASC');
                },
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('school/course/list.html.twig', [
            'datatable' => $table,
        ]);
    }

    /**
     * @Route("/edit/{course}", name="course_edit")
     */
    public function courseEditAction(Request $request, Course $course, ManagerRegistry $doctrine): Response
    {
        $manager = $doctrine->getManager();
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Course $course */
            $course = $form->getData();

            try {
                $manager->flush();
                $this->addFlash('success', 'Zmiany zostały zapisane');

                return $this->redirectToRoute('course_edit', ['course' => $course->getIdCourse()]);
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        }

        return $this->render('school/course/edit.html.twig', [
            'form' => $form->createView(),
            'course' => $course,
        ]);
    }

    /**
     * @Route("/add", name="course_add")
     */
    public function courseAddAction(Request $request, ManagerRegistry $doctrine): Response
    {
        $manager = $doctrine->getManager();
        $form = $this->createForm(CourseType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Course $course */
            $course = $form->getData();
            try {
                $manager->persist($course);

                $manager->flush();
                $this->addFlash('success', 'Kurs został dodany');

                return $this->redirectToRoute('courses_list');
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        }

        return $this->render('school/course/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{course}", name="course_delete")
     */
    public function courseDeleteAction(Request $request, Course $course, ManagerRegistry $doctrine): Response
    {
        if ($request->get('confirm')) {
            try {
                $doctrine->getManager()->remove($course);
                $doctrine->getManager()->flush();
                $this->addFlash('success', 'Kurs został usunięty');

                return $this->redirectToRoute('courses_list');
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        }

        return $this->render('school/course/delete.html.twig', [
            'course' => $course,
        ]);
    }

    /**
     * @Route("/edit_status/{course}", name="course_edit_status")
     */
    public function courseEditStatusAction(Course $course, ManagerRegistry $doctrine): Response
    {
        try {
            if ($course->isActive()) {
                $course->setActive(false);
            } else {
                $course->setActive(true);
            }
            $doctrine->getManager()->flush();
            $this->addFlash('success', 'Status kursu został zmieniony');
        } catch (\Exception $exc) {
            $this->addFlash('error', $exc->getMessage());
        }

        return $this->redirectToRoute('courses_list');
    }
}