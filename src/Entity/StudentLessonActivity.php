<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="student_lesson_activity",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="student_element_unique", columns={"id_student", "id_lesson_element"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\StudentLessonActivityRepository")
 */
class StudentLessonActivity
{
    /**
     * @ORM\Column(name="id_student_course", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $idStudentLessonActivity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Student")
     * @ORM\JoinColumn(name="id_student", referencedColumnName="id_student", nullable=false)
     */
    private Student $student;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\LessonElement")
     * @ORM\JoinColumn(name="id_lesson_element", referencedColumnName="id_lesson_element", nullable=false)
     */
    private LessonElement $lessonElement;

    /**
     * @ORM\Column(name="start_date", type="datetime", nullable=false)
     */
    private \DateTime $start;

    /**
     * @ORM\Column(name="finish_date", type="datetime", nullable=true)
     */
    private ?\DateTime $finish = null;
}
