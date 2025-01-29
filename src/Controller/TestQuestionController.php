<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\TestQuestion;
use App\Entity\TestQuestionAnswer;
use App\Form\TestQuestionType;
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
 * @Route("/questions")
 */
class TestQuestionController extends AbstractController
{
    /**
     * @Route("/list/{course}", name="questions_list")
     */
    public function questionsListAction(Request $request, Course $course, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->setName('question_table')
            ->add('text', TextColumn::class, ['field' => 'q.text', 'searchable' => true])
            ->add('actions', TwigColumn::class, ['template' => 'school/question/_partials/actions_field.html.twig'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => TestQuestion::class,
                'query' => function (QueryBuilder $builder) use ($course) {
                    $builder
                        ->select('q')
                        ->addSelect('a')
                        ->from(TestQuestion::class, 'q')
                        ->join('q.course', 'c')
                        ->leftJoin(TestQuestionAnswer::class, 'a', 'WITH', $builder->expr()->andX('a.question = q', 'a.correct = 1'))
                        ->where('c.idCourse = :idCourse')
                        ->setParameter('idCourse', $course->getIdCourse());
                },
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('school/question/list.html.twig', [
            'course' => $course,
            'datatable' => $table,
        ]);
    }

    /**
     * @Route("/edit/{question}", name="question_edit")
     */
    public function questionEditAction(Request $request, TestQuestion $question, ManagerRegistry $doctrine): Response
    {
        $manager = $doctrine->getManager();
        $form = $this->createForm(TestQuestionType::class, $question);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TestQuestion $question */
            $question = $form->getData();

            try {
                $manager->flush();
                $this->addFlash('success', 'Zmiany zostały zapisane');

                return $this->redirectToRoute('question_edit', ['question' => $question->getIdQuestion()]);
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        }

        return $this->render('school/question/edit.html.twig', [
            'form' => $form->createView(),
            'question' => $question,
        ]);
    }

    /**
     * @Route("/add/{course}", name="question_add")
     */
    public function questionAddAction(Request $request, Course $course, ManagerRegistry $doctrine): Response
    {
        $manager = $doctrine->getManager();
        $form = $this->createForm(TestQuestionType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TestQuestion $question */
            $question = $form->getData();
            $question
                ->setCourse($course);
            try {
                $manager->persist($question);

                $manager->flush();
                $this->addFlash('success', 'Pytanie zostało dodane');

                return $this->redirectToRoute('questions_list', ['course' => $course->getIdCourse()]);
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        }

        return $this->render('school/question/add.html.twig', [
            'form' => $form->createView(),
            'course' => $course,
        ]);
    }

    /**
     * @Route("/delete/{question}", name="question_delete")
     */
    public function questionDeleteAction(Request $request, TestQuestion $question, ManagerRegistry $doctrine): Response
    {
        if ($request->get('confirm')) {
            try {
                $course = $question->getCourse();
                $doctrine->getManager()->remove($question);
                $doctrine->getManager()->flush();
                $this->addFlash('success', 'Pytanie zostało usunięte');

                return $this->redirectToRoute('questions_list', ['course' => $course->getIdCourse()]);
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        }

        return $this->render('school/question/delete.html.twig', [
            'question' => $question,
        ]);
    }
}