<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241209152613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonnementachat ADD abonnement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE abonnementachat ADD CONSTRAINT FK_2BFD717F1D74413 FOREIGN KEY (abonnement_id) REFERENCES abonnement (id)');
        $this->addSql('CREATE INDEX IDX_2BFD717F1D74413 ON abonnementachat (abonnement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonnementachat DROP FOREIGN KEY FK_2BFD717F1D74413');
        $this->addSql('DROP INDEX IDX_2BFD717F1D74413 ON abonnementachat');
        $this->addSql('ALTER TABLE abonnementachat DROP abonnement_id');
    }
}
