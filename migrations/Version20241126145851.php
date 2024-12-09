<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241126145851 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipement ADD equipement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE equipement ADD CONSTRAINT FK_B8B4C6F3806F0F5C FOREIGN KEY (equipement_id) REFERENCES categorie_equipement (id)');
        $this->addSql('CREATE INDEX IDX_B8B4C6F3806F0F5C ON equipement (equipement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipement DROP FOREIGN KEY FK_B8B4C6F3806F0F5C');
        $this->addSql('DROP INDEX IDX_B8B4C6F3806F0F5C ON equipement');
        $this->addSql('ALTER TABLE equipement DROP equipement_id');
    }
}
