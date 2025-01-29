<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241214130410 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE student_test_answer (id_student_test_answer INT AUTO_INCREMENT NOT NULL, id_student INT NOT NULL, id_answer INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_9AE4400869BE0643 (id_student), INDEX IDX_9AE44008FCEAFFC8 (id_answer), PRIMARY KEY(id_student_test_answer)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE student_test_answer ADD CONSTRAINT FK_9AE4400869BE0643 FOREIGN KEY (id_student) REFERENCES student (id_student)');
        $this->addSql('ALTER TABLE student_test_answer ADD CONSTRAINT FK_9AE44008FCEAFFC8 FOREIGN KEY (id_answer) REFERENCES test_question_answer (id_answer)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE student_test_answer DROP FOREIGN KEY FK_9AE4400869BE0643');
        $this->addSql('ALTER TABLE student_test_answer DROP FOREIGN KEY FK_9AE44008FCEAFFC8');
        $this->addSql('DROP TABLE student_test_answer');
    }
}
