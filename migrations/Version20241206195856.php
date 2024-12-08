<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241206195856 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande_produit (id INT AUTO_INCREMENT NOT NULL, commande_produit_id INT DEFAULT NULL, INDEX IDX_DF1E9E8797F6521D (commande_produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande_produit_produit (commande_produit_id INT NOT NULL, produit_id INT NOT NULL, INDEX IDX_899D079B97F6521D (commande_produit_id), INDEX IDX_899D079BF347EFB (produit_id), PRIMARY KEY(commande_produit_id, produit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, mot_de_passe VARCHAR(50) NOT NULL, age INT NOT NULL, email VARCHAR(50) NOT NULL, statut VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande_produit ADD CONSTRAINT FK_DF1E9E8797F6521D FOREIGN KEY (commande_produit_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE commande_produit_produit ADD CONSTRAINT FK_899D079B97F6521D FOREIGN KEY (commande_produit_id) REFERENCES commande_produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commande_produit_produit ADD CONSTRAINT FK_899D079BF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cart ADD utilisateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BA388B7FB88E14F ON cart (utilisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7FB88E14F');
        $this->addSql('ALTER TABLE commande_produit DROP FOREIGN KEY FK_DF1E9E8797F6521D');
        $this->addSql('ALTER TABLE commande_produit_produit DROP FOREIGN KEY FK_899D079B97F6521D');
        $this->addSql('ALTER TABLE commande_produit_produit DROP FOREIGN KEY FK_899D079BF347EFB');
        $this->addSql('DROP TABLE commande_produit');
        $this->addSql('DROP TABLE commande_produit_produit');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP INDEX UNIQ_BA388B7FB88E14F ON cart');
        $this->addSql('ALTER TABLE cart DROP utilisateur_id');
    }
}
