<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(name="lesson")
 * @ORM\Entity(repositoryClass="App\Repository\LessonRepository")
 */
class Lesson
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id_lesson", type="integer", nullable=false)
     */
    private int $idLesson;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Course", inversedBy="lessons")
     * @ORM\JoinColumn(name="id_course", referencedColumnName="id_course", nullable=false)
     */
    private Course $course;

    /**
     * @ORM\Column(name="title", type="text", nullable=false)
     */
    private string $title;

    /**
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private string $description;

    /**
     * @ORM\Column(name="position", type="smallint", nullable=false, options={"default"=1})
     */
    private int $position;

    /**
     * @ORM\Column(name="active", type="boolean", nullable=false, options={"default"=1})
     */
    private bool $active = true;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LessonElement", mappedBy="lesson", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $lessonElements;

    public function __construct()
    {
        $this->lessonElements = new ArrayCollection();
    }

    public function getIdLesson(): int
    {
        return $this->idLesson;
    }

    public function getCourse(): Course
    {
        return $this->course;
    }

    public function setCourse(Course $course): self
    {
        $this->course = $course;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
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

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }
}
