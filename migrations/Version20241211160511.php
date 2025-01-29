<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241211160511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE course (id_course INT AUTO_INCREMENT NOT NULL, title LONGTEXT NOT NULL, description LONGTEXT NOT NULL, difficulty_level SMALLINT DEFAULT 1 NOT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id_course)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson (id_lesson INT AUTO_INCREMENT NOT NULL, id_course INT NOT NULL, title LONGTEXT NOT NULL, description LONGTEXT NOT NULL, position SMALLINT DEFAULT 1 NOT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_F87474F330A9DA54 (id_course), PRIMARY KEY(id_lesson)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson_element (id_lesson_element INT AUTO_INCREMENT NOT NULL, id_lesson INT NOT NULL, content LONGTEXT NOT NULL, position SMALLINT DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_B43C0C9EDE43C11E (id_lesson), PRIMARY KEY(id_lesson_element)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student (id_student INT AUTO_INCREMENT NOT NULL, name VARCHAR(32) NOT NULL, surname VARCHAR(32) NOT NULL, birth_date DATETIME NOT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id_student)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_course (id_student_course INT AUTO_INCREMENT NOT NULL, id_student INT NOT NULL, id_course INT NOT NULL, start_date DATETIME NOT NULL, finish_date DATETIME DEFAULT NULL, INDEX IDX_98A8B73969BE0643 (id_student), INDEX IDX_98A8B73930A9DA54 (id_course), UNIQUE INDEX student_course_unique (id_student, id_course), PRIMARY KEY(id_student_course)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_lesson_activity (id_student_course INT AUTO_INCREMENT NOT NULL, id_student INT NOT NULL, id_lesson_element INT NOT NULL, start_date DATETIME NOT NULL, finish_date DATETIME DEFAULT NULL, INDEX IDX_B897CB6569BE0643 (id_student), INDEX IDX_B897CB65895AB198 (id_lesson_element), UNIQUE INDEX student_element_unique (id_student, id_lesson_element), PRIMARY KEY(id_student_course)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id_user INT AUTO_INCREMENT NOT NULL, id_student INT DEFAULT NULL, password VARCHAR(128) NOT NULL, name VARCHAR(32) NOT NULL, surname VARCHAR(32) NOT NULL, email VARCHAR(128) NOT NULL, last_login_date DATETIME NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_8D93D64969BE0643 (id_student), UNIQUE INDEX email (email), PRIMARY KEY(id_user)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F330A9DA54 FOREIGN KEY (id_course) REFERENCES course (id_course)');
        $this->addSql('ALTER TABLE lesson_element ADD CONSTRAINT FK_B43C0C9EDE43C11E FOREIGN KEY (id_lesson) REFERENCES lesson (id_lesson)');
        $this->addSql('ALTER TABLE student_course ADD CONSTRAINT FK_98A8B73969BE0643 FOREIGN KEY (id_student) REFERENCES student (id_student)');
        $this->addSql('ALTER TABLE student_course ADD CONSTRAINT FK_98A8B73930A9DA54 FOREIGN KEY (id_course) REFERENCES course (id_course)');
        $this->addSql('ALTER TABLE student_lesson_activity ADD CONSTRAINT FK_B897CB6569BE0643 FOREIGN KEY (id_student) REFERENCES student (id_student)');
        $this->addSql('ALTER TABLE student_lesson_activity ADD CONSTRAINT FK_B897CB65895AB198 FOREIGN KEY (id_lesson_element) REFERENCES lesson_element (id_lesson_element)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64969BE0643 FOREIGN KEY (id_student) REFERENCES student (id_student)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F330A9DA54');
        $this->addSql('ALTER TABLE lesson_element DROP FOREIGN KEY FK_B43C0C9EDE43C11E');
        $this->addSql('ALTER TABLE student_course DROP FOREIGN KEY FK_98A8B73969BE0643');
        $this->addSql('ALTER TABLE student_course DROP FOREIGN KEY FK_98A8B73930A9DA54');
        $this->addSql('ALTER TABLE student_lesson_activity DROP FOREIGN KEY FK_B897CB6569BE0643');
        $this->addSql('ALTER TABLE student_lesson_activity DROP FOREIGN KEY FK_B897CB65895AB198');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64969BE0643');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('DROP TABLE lesson_element');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE student_course');
        $this->addSql('DROP TABLE student_lesson_activity');
        $this->addSql('DROP TABLE user');
    }
}
