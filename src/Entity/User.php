<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(
 *     name="user",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="email", columns={"email"})
 *     }
 * )
 * @ORM\Entity
 */
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id_user", type="integer", nullable=false)
     */
    private int $idUser;

    /**
     * @ORM\Column(name="password", type="string", length=128, nullable=false)
     */
    private string $password;

    /**
     * @ORM\Column(name="name", type="string", length=32, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(name="surname", type="string", length=32, nullable=false)
     */
    private string $surname;

    /**
     * @ORM\Column(name="email", type="string", length=128, nullable=false)
     */
    private string $email;

    /**
     * @ORM\Column(name="last_login_date", type="datetime", nullable=false)
     */
    private \DateTime $lastLoginDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Student")
     * @ORM\JoinColumn(name="id_student", referencedColumnName="id_student", nullable=true)
     */
    private ?Student $student;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles;

    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
    }

    public function getIdUser(): int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): User
    {
        $this->idUser = $idUser;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): User
    {
        $this->password = $password;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): User
    {
        $this->name = $name;
        return $this;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): User
    {
        $this->surname = $surname;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    public function getLastLoginDate(): \DateTime
    {
        return $this->lastLoginDate;
    }

    public function setLastLoginDate(\DateTime $lastLoginDate): User
    {
        $this->lastLoginDate = $lastLoginDate;
        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): User
    {
        $this->student = $student;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): User
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials()
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }
}
