<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(name="lesson_element")
 * @ORM\Entity(repositoryClass="App\Repository\LessonElementRepository")
 */
class LessonElement
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id_lesson_element", type="integer", nullable=false)
     */
    private int $idLessonElement;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lesson", inversedBy="lessonElements")
     * @ORM\JoinColumn(name="id_lesson", referencedColumnName="id_lesson", nullable=false)
     */
    private Lesson $lesson;

    /**
     * @ORM\Column(name="content", type="text", nullable=false)
     */
    private string $content;

    /**
     * @ORM\Column(name="position", type="smallint", nullable=false, options={"default"=1})
     */
    private int $position;

    public function getIdLessonElement(): int
    {
        return $this->idLessonElement;
    }

    public function getLesson(): Lesson
    {
        return $this->lesson;
    }

    public function setLesson(Lesson $lesson): self
    {
        $this->lesson = $lesson;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }
}
