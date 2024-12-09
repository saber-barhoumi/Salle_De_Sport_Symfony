<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241208151913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE equipement_history (id INT AUTO_INCREMENT NOT NULL, equipement_id INT DEFAULT NULL, action VARCHAR(255) NOT NULL, date DATETIME NOT NULL, user VARCHAR(255) NOT NULL, INDEX IDX_2C373A98806F0F5C (equipement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE equipement_history ADD CONSTRAINT FK_2C373A98806F0F5C FOREIGN KEY (equipement_id) REFERENCES equipement (id)');
        $this->addSql('ALTER TABLE categorie_equipement DROP description, DROP image');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipement_history DROP FOREIGN KEY FK_2C373A98806F0F5C');
        $this->addSql('DROP TABLE equipement_history');
        $this->addSql('ALTER TABLE categorie_equipement ADD description LONGTEXT DEFAULT NULL, ADD image VARCHAR(255) NOT NULL');
    }
}
