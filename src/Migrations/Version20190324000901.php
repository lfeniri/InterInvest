<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190324000901 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE legal_form (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company ADD legal_form_id INT NOT NULL');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F98CD0513 FOREIGN KEY (legal_form_id) REFERENCES legal_form (id)');
        $this->addSql('CREATE INDEX IDX_4FBF094F98CD0513 ON company (legal_form_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F98CD0513');
        $this->addSql('DROP TABLE legal_form');
        $this->addSql('DROP INDEX IDX_4FBF094F98CD0513 ON company');
        $this->addSql('ALTER TABLE company DROP legal_form_id');
    }
}
