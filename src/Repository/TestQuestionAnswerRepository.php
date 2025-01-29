<?php

namespace App\Repository;

use App\Entity\TestQuestion;
use App\Entity\TestQuestionAnswer;
use Doctrine\ORM\EntityRepository;

class TestQuestionAnswerRepository extends EntityRepository
{
    public function setAllAnswersIncorrect(TestQuestion $question, TestQuestionAnswer $correctAnswer): void
    {
        $query = $this->createQueryBuilder('a')
            ->update()
            ->set('a.correct', ':false')
            ->where('a.question = :question')
            ->andWhere('a.idAnswer != :correctAnswer')
            ->setParameter('false', false)
            ->setParameter('question', $question->getIdQuestion())
            ->setParameter('correctAnswer', $correctAnswer->getIdAnswer());

        $query->getQuery()->execute();
    }
}