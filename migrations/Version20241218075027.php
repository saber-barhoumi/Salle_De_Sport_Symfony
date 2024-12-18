<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241218075027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE seance_utilisateur DROP FOREIGN KEY FK_20CEA8F8E3797A94');
        $this->addSql('ALTER TABLE seance_utilisateur DROP FOREIGN KEY FK_20CEA8F8FB88E14F');
        $this->addSql('DROP TABLE seance_utilisateur');
        $this->addSql('ALTER TABLE abonnementachat DROP FOREIGN KEY FK_2BFD717FB88E14F');
        $this->addSql('DROP INDEX IDX_2BFD717FB88E14F ON abonnementachat');
        $this->addSql('ALTER TABLE abonnementachat DROP utilisateur_id');
        $this->addSql('ALTER TABLE commande_produit DROP FOREIGN KEY FK_DF1E9E87FB88E14F');
        $this->addSql('DROP INDEX IDX_DF1E9E87FB88E14F ON commande_produit');
        $this->addSql('ALTER TABLE commande_produit DROP utilisateur_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE seance_utilisateur (seance_id INT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_20CEA8F8E3797A94 (seance_id), INDEX IDX_20CEA8F8FB88E14F (utilisateur_id), PRIMARY KEY(seance_id, utilisateur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE seance_utilisateur ADD CONSTRAINT FK_20CEA8F8E3797A94 FOREIGN KEY (seance_id) REFERENCES seance (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE seance_utilisateur ADD CONSTRAINT FK_20CEA8F8FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE abonnementachat ADD utilisateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE abonnementachat ADD CONSTRAINT FK_2BFD717FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_2BFD717FB88E14F ON abonnementachat (utilisateur_id)');
        $this->addSql('ALTER TABLE commande_produit ADD utilisateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commande_produit ADD CONSTRAINT FK_DF1E9E87FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_DF1E9E87FB88E14F ON commande_produit (utilisateur_id)');
    }
}
