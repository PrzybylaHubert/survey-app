<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250416220828 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE survey (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, is_active TINYINT(1) NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_AD5F9BFCF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE survey_assignment (id INT AUTO_INCREMENT NOT NULL, survey_id INT NOT NULL, user_id INT NOT NULL, date_start DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', date_finish DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75BF4E8DB3FE509D (survey_id), INDEX IDX_75BF4E8DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE survey_offered_answer (id INT AUTO_INCREMENT NOT NULL, question_id INT NOT NULL, offered_answer VARCHAR(511) NOT NULL, INDEX IDX_FE6E98731E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE survey_question (id INT AUTO_INCREMENT NOT NULL, section_id INT NOT NULL, question_type VARCHAR(255) NOT NULL, question VARCHAR(511) NOT NULL, limiter SMALLINT DEFAULT NULL, INDEX IDX_EA000F69D823E37A (section_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE survey_section (id INT AUTO_INCREMENT NOT NULL, survey_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_695F5954B3FE509D (survey_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE survey_user_answer (id INT AUTO_INCREMENT NOT NULL, offered_answer_id INT DEFAULT NULL, question_id INT NOT NULL, survey_assignment_id INT NOT NULL, value VARCHAR(255) DEFAULT NULL, INDEX IDX_7778E1DF093B751 (offered_answer_id), INDEX IDX_7778E1D1E27F6BF (question_id), INDEX IDX_7778E1DBA888100 (survey_assignment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE survey ADD CONSTRAINT FK_AD5F9BFCF675F31B FOREIGN KEY (author_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE survey_assignment ADD CONSTRAINT FK_75BF4E8DB3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE survey_assignment ADD CONSTRAINT FK_75BF4E8DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE survey_offered_answer ADD CONSTRAINT FK_FE6E98731E27F6BF FOREIGN KEY (question_id) REFERENCES survey_question (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE survey_question ADD CONSTRAINT FK_EA000F69D823E37A FOREIGN KEY (section_id) REFERENCES survey_section (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE survey_section ADD CONSTRAINT FK_695F5954B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE survey_user_answer ADD CONSTRAINT FK_7778E1DF093B751 FOREIGN KEY (offered_answer_id) REFERENCES survey_offered_answer (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE survey_user_answer ADD CONSTRAINT FK_7778E1D1E27F6BF FOREIGN KEY (question_id) REFERENCES survey_question (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE survey_user_answer ADD CONSTRAINT FK_7778E1DBA888100 FOREIGN KEY (survey_assignment_id) REFERENCES survey_assignment (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE survey DROP FOREIGN KEY FK_AD5F9BFCF675F31B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE survey_assignment DROP FOREIGN KEY FK_75BF4E8DB3FE509D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE survey_assignment DROP FOREIGN KEY FK_75BF4E8DA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE survey_offered_answer DROP FOREIGN KEY FK_FE6E98731E27F6BF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE survey_question DROP FOREIGN KEY FK_EA000F69D823E37A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE survey_section DROP FOREIGN KEY FK_695F5954B3FE509D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE survey_user_answer DROP FOREIGN KEY FK_7778E1DF093B751
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE survey_user_answer DROP FOREIGN KEY FK_7778E1D1E27F6BF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE survey_user_answer DROP FOREIGN KEY FK_7778E1DBA888100
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE survey
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE survey_assignment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE survey_offered_answer
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE survey_question
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE survey_section
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE survey_user_answer
        SQL);
    }
}
