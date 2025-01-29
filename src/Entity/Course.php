<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(name="course")
 * @ORM\Entity(repositoryClass="App\Repository\CourseRepository")
 */
class Course
{
    use TimestampableEntity;

    public const
        LEVEL_BEGINNER = 1,
        LEVEL_INTERMEDIATE = 2,
        LEVEL_ADVANCED = 3,
        LEVEL_EXPERT = 4;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id_course", type="integer", nullable=false)
     */
    private int $idCourse;

    /**
     * @ORM\Column(name="title", type="text", nullable=false)
     */
    private string $title;

    /**
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private string $description;

    /**
     * @ORM\Column(name="difficulty_level", type="smallint", nullable=false, options={"default"=1})
     */
    private int $level;

    /**
     * @ORM\Column(name="active", type="boolean", nullable=false, options={"default"=1})
     */
    private bool $active = true;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Lesson", mappedBy="course", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $lessons;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TestQuestion", mappedBy="course", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $questions;

    public function __construct()
    {
        $this->lessons = new ArrayCollection();
        $this->questions = new ArrayCollection();
    }

    public function getIdCourse(): int
    {
        return $this->idCourse;
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

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;
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

    public static function getLevels(): array
    {
        $types = [self::LEVEL_BEGINNER, self::LEVEL_INTERMEDIATE, self::LEVEL_ADVANCED, self::LEVEL_EXPERT];
        $names = [];

        foreach ($types as $type) {
            $names[$type] = 'course.level.' . $type;
        }

        return $names;
    }
}
