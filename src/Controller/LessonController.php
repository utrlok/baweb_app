<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Form\LessonType;
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
 * @Route("/lesson")
 */
class LessonController extends AbstractController
{
    /**
     * @Route("/list/{course}", name="lessons_list")
     */
    public function lessonsListAction(Request $request, Course $course, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->setName('lesson_table')
            ->add('title', TextColumn::class, ['field' => 'l.title', 'searchable' => true])
            ->add('position', TwigColumn::class, ['template' => 'school/lesson/_partials/position_field.html.twig', 'searchable' => false])
            ->add('active', TwigColumn::class, ['template' => 'school/lesson/_partials/active_field.html.twig', 'searchable' => false])
            ->add('actions', TwigColumn::class, ['template' => 'school/lesson/_partials/actions_field.html.twig'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Lesson::class,
                'query' => function (QueryBuilder $builder) use ($course) {
                    $builder
                        ->select('l')
                        ->from(Lesson::class, 'l')
                        ->join('l.course', 'c')
                        ->where('c.idCourse = :idCourse')
                        ->setParameter('idCourse', $course->getIdCourse())
                        ->orderBy('l.position', 'ASC');
                },
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('school/lesson/list.html.twig', [
            'course' => $course,
            'datatable' => $table,
        ]);
    }

    /**
     * @Route("/edit/{lesson}", name="lesson_edit")
     */
    public function lessonEditAction(Request $request, Lesson $lesson, ManagerRegistry $doctrine): Response
    {
        $manager = $doctrine->getManager();
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Lesson $lesson */
            $lesson = $form->getData();

            try {
                $manager->flush();
                $this->addFlash('success', 'Zmiany zostały zapisane');

                return $this->redirectToRoute('lesson_edit', ['lesson' => $lesson->getIdLesson()]);
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        }

        return $this->render('school/lesson/edit.html.twig', [
            'form' => $form->createView(),
            'lesson' => $lesson,
        ]);
    }

    /**
     * @Route("/add/{course}", name="lesson_add")
     */
    public function lessonAddAction(Request $request, Course $course, ManagerRegistry $doctrine): Response
    {
        $manager = $doctrine->getManager();
        $form = $this->createForm(LessonType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Lesson $lesson */
            $lesson = $form->getData();
            $nextPosition = $doctrine->getManager()->getRepository(Lesson::class)->getMaxPosition();
            $lesson
                ->setCourse($course)
                ->setPosition($nextPosition + 1);
            try {
                $manager->persist($lesson);

                $manager->flush();
                $this->addFlash('success', 'Lekcja została dodana');

                return $this->redirectToRoute('lessons_list', ['course' => $course->getIdCourse()]);
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        }

        return $this->render('school/lesson/add.html.twig', [
            'form' => $form->createView(),
            'course' => $course,
        ]);
    }

    /**
     * @Route("/delete/{lesson}", name="lesson_delete")
     */
    public function lessonDeleteAction(Request $request, Lesson $lesson, ManagerRegistry $doctrine): Response
    {
        if ($request->get('confirm')) {
            try {
                $course = $lesson->getCourse();
                $doctrine->getManager()->remove($lesson);
                $doctrine->getManager()->flush();
                $this->addFlash('success', 'Lekcja została usunięta');

                return $this->redirectToRoute('lessons_list', ['course' => $course->getIdCourse()]);
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        }

        return $this->render('school/lesson/delete.html.twig', [
            'lesson' => $lesson,
        ]);
    }

    /**
     * @Route("/edit_status/{lesson}", name="lesson_edit_status")
     */
    public function lessonEditStatusAction(Lesson $lesson, ManagerRegistry $doctrine): Response
    {
        try {
            if ($lesson->isActive()) {
                $lesson->setActive(false);
            } else {
                $lesson->setActive(true);
            }
            $doctrine->getManager()->flush();
            $this->addFlash('success', 'Status lekcji został zmieniony');
        } catch (\Exception $exc) {
            $this->addFlash('error', $exc->getMessage());
        }

        return $this->redirectToRoute('lessons_list', ['course' => $lesson->getCourse()->getIdCourse()]);
    }

    /**
     * @Route("/move/{lesson}/{direction}", name="lesson_move")
     */
    public function moveLessonAction(Lesson $lesson, string $direction, ManagerRegistry $doctrine): Response
    {
        $manager = $doctrine->getManager();
        $lessonRepository = $manager->getRepository(Lesson::class);
        $currentPosition = $lesson->getPosition();
        $swapLesson = null;

        if ($direction === 'up') {
            $swapLesson = $lessonRepository->findOneBy(['course' => $lesson->getCourse(), 'position' => $currentPosition - 1]);
        } elseif ($direction === 'down') {
            $swapLesson = $lessonRepository->findOneBy(['course' => $lesson->getCourse(), 'position' => $currentPosition + 1]);
        }

        if ($swapLesson) {
            $lesson->setPosition($swapLesson->getPosition());
            $swapLesson->setPosition($currentPosition);

            try {
                $manager->persist($lesson);
                $manager->persist($swapLesson);
                $manager->flush();

                $this->addFlash('success', 'Pozycja lekcji została zmieniona');
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        } else {
            $this->addFlash('error', 'Nie można zmienić pozycji lekcji');
        }

        return $this->redirectToRoute('lessons_list', ['course' => $lesson->getCourse()->getIdCourse()]);
    }

}