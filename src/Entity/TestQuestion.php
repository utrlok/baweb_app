<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(name="test_question")
 * @ORM\Entity(repositoryClass="App\Repository\TestQuestionRepository")
 */
class TestQuestion
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id_question", type="integer", nullable=false)
     */
    private int $idQuestion;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Course", inversedBy="questions")
     * @ORM\JoinColumn(name="id_course", referencedColumnName="id_course", nullable=false)
     */
    private Course $course;

    /**
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private string $text;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TestQuestionAnswer", mappedBy="question", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $answers;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    public function getIdQuestion(): int
    {
        return $this->idQuestion;
    }

    public function setIdQuestion(int $idQuestion): TestQuestion
    {
        $this->idQuestion = $idQuestion;
        return $this;
    }

    public function getCourse(): Course
    {
        return $this->course;
    }

    public function setCourse(Course $course): TestQuestion
    {
        $this->course = $course;
        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): TestQuestion
    {
        $this->text = $text;
        return $this;
    }

    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function setAnswers(Collection $answers): TestQuestion
    {
        $this->answers = $answers;
        return $this;
    }
}
