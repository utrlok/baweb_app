<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(name="student")
 * @ORM\Entity(repositoryClass="App\Repository\StudentRepository")
 */
class Student
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id_student", type="integer", nullable=false)
     */
    private int $idStudent;

    /**
     * @ORM\Column(name="name", type="string", length=32, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(name="surname", type="string", length=32, nullable=false)
     */
    private string $surname;

    /**
     * @ORM\Column(name="birth_date", type="datetime", nullable=false)
     */
    private \DateTime $birthDate;

    /**
     * @ORM\Column(name="active", type="boolean", nullable=false, options={"default"=1})
     */
    private bool $active = true;

    public function getFullName(): string
    {
        return sprintf('%s %s', $this->getName(), $this->getSurname());
    }

    public function getIdStudent(): int
    {
        return $this->idStudent;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;
        return $this;
    }

    public function getBirthDate(): \DateTime
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTime $birthDate): self
    {
        $this->birthDate = $birthDate;
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