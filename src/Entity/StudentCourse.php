<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="student_course",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="student_course_unique", columns={"id_student", "id_course"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\StudentLessonActivityRepository")
 */
class StudentCourse
{
    /**
     * @ORM\Column(name="id_student_course", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $idStudentCourse;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Student")
     * @ORM\JoinColumn(name="id_student", referencedColumnName="id_student", nullable=false)
     */
    private Student $student;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Course")
     * @ORM\JoinColumn(name="id_course", referencedColumnName="id_course", nullable=false)
     */
    private Course $course;

    /**
     * @ORM\Column(name="start_date", type="datetime", nullable=false)
     */
    private \DateTime $start;

    /**
     * @ORM\Column(name="finish_date", type="datetime", nullable=true)
     */
    private ?\DateTime $finish = null;

    public function getIdStudentCourse(): int
    {
        return $this->idStudentCourse;
    }

    public function setIdStudentCourse(int $idStudentCourse): StudentCourse
    {
        $this->idStudentCourse = $idStudentCourse;
        return $this;
    }

    public function getStudent(): Student
    {
        return $this->student;
    }

    public function setStudent(Student $student): StudentCourse
    {
        $this->student = $student;
        return $this;
    }

    public function getCourse(): Course
    {
        return $this->course;
    }

    public function setCourse(Course $course): StudentCourse
    {
        $this->course = $course;
        return $this;
    }

    public function getStart(): \DateTime
    {
        return $this->start;
    }

    public function setStart(\DateTime $start): StudentCourse
    {
        $this->start = $start;
        return $this;
    }

    public function getFinish(): ?\DateTime
    {
        return $this->finish;
    }

    public function setFinish(?\DateTime $finish): StudentCourse
    {
        $this->finish = $finish;
        return $this;
    }
}
