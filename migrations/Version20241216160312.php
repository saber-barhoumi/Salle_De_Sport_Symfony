<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241216160312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE type_utilisateur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE utilisateur ADD type_utilisateur_id INT DEFAULT NULL, ADD is_verified TINYINT(1) NOT NULL, ADD reset_token VARCHAR(255) DEFAULT NULL, ADD role VARCHAR(50) NOT NULL, CHANGE mot_de_passe mot_de_passe VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(50) NOT NULL, CHANGE status statut VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3AD4BC8DB FOREIGN KEY (type_utilisateur_id) REFERENCES type_utilisateur (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B3E7927C74 ON utilisateur (email)');
        $this->addSql('CREATE INDEX IDX_1D1C63B3AD4BC8DB ON utilisateur (type_utilisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3AD4BC8DB');
        $this->addSql('DROP TABLE type_utilisateur');
        $this->addSql('DROP INDEX UNIQ_1D1C63B3E7927C74 ON utilisateur');
        $this->addSql('DROP INDEX IDX_1D1C63B3AD4BC8DB ON utilisateur');
        $this->addSql('ALTER TABLE utilisateur ADD status VARCHAR(50) NOT NULL, DROP type_utilisateur_id, DROP statut, DROP is_verified, DROP reset_token, DROP role, CHANGE mot_de_passe mot_de_passe VARCHAR(50) NOT NULL, CHANGE email email VARCHAR(100) NOT NULL');
    }
}
