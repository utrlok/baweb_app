<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\LessonElement;
use App\Form\LessonElementType;
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
 * @Route("/lesson_element")
 */
class LessonElementController extends AbstractController
{
    /**
     * @Route("/list/{lesson}", name="lesson_elements_list")
     */
    public function lessonElementsListAction(Request $request, Lesson $lesson, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->setName('lesson_elements_table')
            ->add('position', TwigColumn::class, ['template' => 'school/lesson_element/_partials/position_field.html.twig', 'searchable' => false])
            ->add('actions', TwigColumn::class, ['template' => 'school/lesson_element/_partials/actions_field.html.twig'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => LessonElement::class,
                'query' => function (QueryBuilder $builder) use ($lesson) {
                    $builder
                        ->select('le')
                        ->from(LessonElement::class, 'le')
                        ->join('le.lesson', 'l')
                        ->where('l.idLesson = :idLesson')
                        ->setParameter('idLesson', $lesson->getIdLesson())
                        ->orderBy('le.position', 'ASC');
                },
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('school/lesson_element/list.html.twig', [
            'lesson' => $lesson,
            'datatable' => $table,
        ]);
    }

    /**
     * @Route("/edit/{lessonElement}", name="lesson_element_edit")
     */
    public function lessonElementEditAction(Request $request, LessonElement $lessonElement, ManagerRegistry $doctrine): Response
    {
        $manager = $doctrine->getManager();
        $form = $this->createForm(LessonElementType::class, $lessonElement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var LessonElement $lessonElement */
            $lessonElement = $form->getData();

            try {
                $manager->flush();
                $this->addFlash('success', 'Zmiany zostały zapisane');

                return $this->redirectToRoute('lesson_element_edit', ['lessonElement' => $lessonElement->getIdLessonElement()]);
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        }

        return $this->render('school/lesson_element/edit.html.twig', [
            'form' => $form->createView(),
            'lessonElement' => $lessonElement,
        ]);
    }

    /**
     * @Route("/preview/{lessonElement}", name="lesson_element_preview")
     */
    public function lessonElementPreviewAction(LessonElement $lessonElement): Response
    {
        return $this->render('school/lesson_element/preview.html.twig', [
            'lessonElement' => $lessonElement,
        ]);
    }

    /**
     * @Route("/add/{lesson}", name="lesson_element_add")
     */
    public function lessonElementAddAction(Request $request, Lesson $lesson, ManagerRegistry $doctrine): Response
    {
        $manager = $doctrine->getManager();
        $form = $this->createForm(LessonElementType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var LessonElement $lessonElement */
            $lessonElement = $form->getData();
            $nextPosition = $doctrine->getManager()->getRepository(LessonElement::class)->getMaxPosition();
            $lessonElement
                ->setLesson($lesson)
                ->setPosition($nextPosition + 1);
            try {
                $manager->persist($lessonElement);

                $manager->flush();
                $this->addFlash('success', 'Element lekcji została dodana');

                return $this->redirectToRoute('lesson_elements_list', ['lesson' => $lesson->getIdLesson()]);
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        }

        return $this->render('school/lesson_element/add.html.twig', [
            'form' => $form->createView(),
            'lesson' => $lesson,
        ]);
    }

    /**
     * @Route("/delete/{lessonElement}", name="lesson_element_delete")
     */
    public function lessonElementDeleteAction(Request $request, LessonElement $lessonElement, ManagerRegistry $doctrine): Response
    {
        if ($request->get('confirm')) {
            try {
                $lesson = $lessonElement->getLesson();
                dump($lessonElement);
                $doctrine->getManager()->remove($lessonElement);
                $doctrine->getManager()->flush();
                dump($lesson);
                $this->addFlash('success', 'Lekcja została usunięta');

                return $this->redirectToRoute('lesson_elements_list', ['lesson' => $lesson->getIdLesson()]);
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        }

        return $this->render('school/lesson_element/delete.html.twig', [
            'lessonElement' => $lessonElement,
        ]);
    }

    /**
     * @Route("/move/{lessonElement}/{direction}", name="lesson_element_move")
     */
    public function moveLessonAction(LessonElement $lessonElement, string $direction, ManagerRegistry $doctrine): Response
    {
        $manager = $doctrine->getManager();
        $lessonElementRepository = $manager->getRepository(LessonElement::class);
        $currentPosition = $lessonElement->getPosition();
        $swapLesson = null;

        if ($direction === 'up') {
            $swapLesson = $lessonElementRepository->findOneBy(['lesson' => $lessonElement->getLesson(), 'position' => $currentPosition - 1]);
        } elseif ($direction === 'down') {
            $swapLesson = $lessonElementRepository->findOneBy(['lesson' => $lessonElement->getLesson(), 'position' => $currentPosition + 1]);
        }

        if ($swapLesson) {
            $lessonElement->setPosition($swapLesson->getPosition());
            $swapLesson->setPosition($currentPosition);

            try {
                $manager->persist($lessonElement);
                $manager->persist($swapLesson);
                $manager->flush();

                $this->addFlash('success', 'Pozycja została zmieniona');
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        } else {
            $this->addFlash('error', 'Nie można zmienić pozycji');
        }

        return $this->redirectToRoute('lesson_elements_list', ['lesson' => $lessonElement->getLesson()->getIdLesson()]);
    }

}