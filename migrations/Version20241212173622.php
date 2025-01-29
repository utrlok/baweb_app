<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241212173622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE test_question (id_question INT AUTO_INCREMENT NOT NULL, id_course INT NOT NULL, text LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_2394421830A9DA54 (id_course), PRIMARY KEY(id_question)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE test_question_answer (id_answer INT AUTO_INCREMENT NOT NULL, id_question INT NOT NULL, text LONGTEXT NOT NULL, correct TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_A34E5568E62CA5DB (id_question), PRIMARY KEY(id_answer)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE test_question ADD CONSTRAINT FK_2394421830A9DA54 FOREIGN KEY (id_course) REFERENCES course (id_course)');
        $this->addSql('ALTER TABLE test_question_answer ADD CONSTRAINT FK_A34E5568E62CA5DB FOREIGN KEY (id_question) REFERENCES test_question (id_question)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE test_question DROP FOREIGN KEY FK_2394421830A9DA54');
        $this->addSql('ALTER TABLE test_question_answer DROP FOREIGN KEY FK_A34E5568E62CA5DB');
        $this->addSql('DROP TABLE test_question');
        $this->addSql('DROP TABLE test_question_answer');
    }
}
