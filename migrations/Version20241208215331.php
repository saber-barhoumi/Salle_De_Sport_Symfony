<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241208215331 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipement_history DROP FOREIGN KEY FK_2C373A98806F0F5C');
        $this->addSql('ALTER TABLE equipement_history ADD equipement_nom VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE equipement_history ADD CONSTRAINT FK_2C373A98806F0F5C FOREIGN KEY (equipement_id) REFERENCES equipement (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipement_history DROP FOREIGN KEY FK_2C373A98806F0F5C');
        $this->addSql('ALTER TABLE equipement_history DROP equipement_nom');
        $this->addSql('ALTER TABLE equipement_history ADD CONSTRAINT FK_2C373A98806F0F5C FOREIGN KEY (equipement_id) REFERENCES equipement (id) ON DELETE SET NULL');
    }
}
