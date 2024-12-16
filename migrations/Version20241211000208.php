<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241211000208 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonnementachat ADD type_abonnement_id INT NOT NULL, ADD prix NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE abonnementachat ADD CONSTRAINT FK_2BFD717813AF326 FOREIGN KEY (type_abonnement_id) REFERENCES typeabonnement (id)');
        $this->addSql('CREATE INDEX IDX_2BFD717813AF326 ON abonnementachat (type_abonnement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonnementachat DROP FOREIGN KEY FK_2BFD717813AF326');
        $this->addSql('DROP INDEX IDX_2BFD717813AF326 ON abonnementachat');
        $this->addSql('ALTER TABLE abonnementachat DROP type_abonnement_id, DROP prix');
    }
}
