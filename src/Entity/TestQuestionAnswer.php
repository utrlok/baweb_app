<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(name="test_question_answer")
 * @ORM\Entity(repositoryClass="App\Repository\TestQuestionAnswerRepository")
 */
class TestQuestionAnswer
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id_answer", type="integer", nullable=false)
     */
    private int $idAnswer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TestQuestion", inversedBy="answers")
     * @ORM\JoinColumn(name="id_question", referencedColumnName="id_question", nullable=false)
     */
    private TestQuestion $question;

    /**
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private string $text;

    /**
     * @ORM\Column(name="correct", type="boolean", nullable=false, options={"default"=0})
     */
    private bool $correct = true;

    public function getIdAnswer(): int
    {
        return $this->idAnswer;
    }

    public function setIdAnswer(int $idAnswer): TestQuestionAnswer
    {
        $this->idAnswer = $idAnswer;
        return $this;
    }

    public function getQuestion(): TestQuestion
    {
        return $this->question;
    }

    public function setQuestion(TestQuestion $question): TestQuestionAnswer
    {
        $this->question = $question;
        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): TestQuestionAnswer
    {
        $this->text = $text;
        return $this;
    }

    public function isCorrect(): bool
    {
        return $this->correct;
    }

    public function setCorrect(bool $correct): TestQuestionAnswer
    {
        $this->correct = $correct;
        return $this;
    }
}
