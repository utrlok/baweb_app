<?php

namespace App\Controller;

use App\Entity\TestQuestion;
use App\Entity\TestQuestionAnswer;
use App\Form\TestQuestionAnswerType;
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
 * @Route("/answers")
 */
class TestQuestionAnswerController extends AbstractController
{
    /**
     * @Route("/list/{question}", name="answers_list")
     */
    public function answersListAction(Request $request, TestQuestion $question, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->setName('answer_table')
            ->add('text', TextColumn::class, ['field' => 'a.text', 'searchable' => true])
            ->add('correct', TwigColumn::class, ['template' => 'school/answer/_partials/correct.html.twig'])
            ->add('actions', TwigColumn::class, ['template' => 'school/answer/_partials/actions_field.html.twig'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => TestQuestionAnswer::class,
                'query' => function (QueryBuilder $builder) use ($question) {
                    $builder
                        ->select('a')
                        ->from(TestQuestionAnswer::class, 'a')
                        ->join('a.question', 'q')
                        ->where('q.idQuestion = :idQuestion')
                        ->setParameter('idQuestion', $question->getIdQuestion());
                },
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('school/answer/list.html.twig', [
            'question' => $question,
            'datatable' => $table,
        ]);
    }

    /**
     * @Route("/edit/{answer}", name="answer_edit")
     */
    public function answerEditAction(Request $request, TestQuestionAnswer $answer, ManagerRegistry $doctrine): Response
    {
        $manager = $doctrine->getManager();
        $form = $this->createForm(TestQuestionAnswerType::class, $answer);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TestQuestionAnswer $answer */
            $answer = $form->getData();

            if ($answer->isCorrect()) {
                $doctrine->getRepository(TestQuestionAnswer::class)->setAllAnswersIncorrect($answer->getQuestion(), $answer);
            }

            try {
                $manager->flush();
                $this->addFlash('success', 'Zmiany zostały zapisane');

                return $this->redirectToRoute('answer_edit', ['answer' => $answer->getIdAnswer()]);
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        }

        return $this->render('school/answer/edit.html.twig', [
            'form' => $form->createView(),
            'answer' => $answer,
        ]);
    }

    /**
     * @Route("/add/{question}", name="answer_add")
     */
    public function answerAddAction(Request $request, TestQuestion $question, ManagerRegistry $doctrine): Response
    {
        $manager = $doctrine->getManager();
        $form = $this->createForm(TestQuestionAnswerType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TestQuestionAnswer $answer */
            $answer = $form->getData();
            $answer
                ->setQuestion($question);

            try {
                $manager->persist($answer);
                $manager->flush();

                if ($answer->isCorrect()) {
                    $doctrine->getRepository(TestQuestionAnswer::class)->setAllAnswersIncorrect($question, $answer);
                }

                $this->addFlash('success', 'Odpowiedź została dodana');

                return $this->redirectToRoute('answers_list', ['question' => $question->getIdQuestion()]);
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        }

        return $this->render('school/answer/add.html.twig', [
            'form' => $form->createView(),
            'question' => $question,
        ]);
    }

    /**
     * @Route("/delete/{answer}", name="answer_delete")
     */
    public function answerDeleteAction(Request $request, TestQuestionAnswer $answer, ManagerRegistry $doctrine): Response
    {
        if ($request->get('confirm')) {
            try {
                $question = $answer->getQuestion();
                $doctrine->getManager()->remove($answer);
                $doctrine->getManager()->flush();
                $this->addFlash('success', 'Odpowiedź została usunięta');

                return $this->redirectToRoute('answers_list', ['question' => $question->getIdQuestion()]);
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        }

        return $this->render('school/answer/delete.html.twig', [
            'answer' => $answer,
        ]);
    }
}