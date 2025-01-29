<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(
 *     name="student_test_answer",
 * )
 * @ORM\Entity()
 */
class StudentTestAnswer
{
    use TimestampableEntity;

    /**
     * @ORM\Column(name="id_student_test_answer", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $idStudentTestAnswer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Student")
     * @ORM\JoinColumn(name="id_student", referencedColumnName="id_student", nullable=false)
     */
    private Student $student;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TestQuestionAnswer")
     * @ORM\JoinColumn(name="id_answer", referencedColumnName="id_answer", nullable=false)
     */
    private TestQuestionAnswer $course;
}
