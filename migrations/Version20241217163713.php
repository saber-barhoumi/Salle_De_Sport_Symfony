<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241217163713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE abonnement (id INT AUTO_INCREMENT NOT NULL, type_abonnement_id INT NOT NULL, autorenouvellement TINYINT(1) NOT NULL, commentaires VARCHAR(255) NOT NULL, sport VARCHAR(50) NOT NULL, prix DOUBLE PRECISION NOT NULL, capacite INT NOT NULL, INDEX IDX_351268BB813AF326 (type_abonnement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE abonnementachat (id INT AUTO_INCREMENT NOT NULL, abonnement_id INT NOT NULL, date_achat DATETIME NOT NULL, INDEX IDX_2BFD717F1D74413 (abonnement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie_equipement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipement (id INT AUTO_INCREMENT NOT NULL, equipement_id INT DEFAULT NULL, nom VARCHAR(50) NOT NULL, prix DOUBLE PRECISION DEFAULT NULL, etat VARCHAR(50) NOT NULL, fournisseur VARCHAR(50) NOT NULL, photo VARCHAR(255) DEFAULT NULL, descriptions LONGTEXT NOT NULL, INDEX IDX_B8B4C6F3806F0F5C (equipement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipement_history (id INT AUTO_INCREMENT NOT NULL, equipement_id INT DEFAULT NULL, action VARCHAR(255) NOT NULL, date DATETIME NOT NULL, user VARCHAR(255) NOT NULL, INDEX IDX_2C373A98806F0F5C (equipement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, equipement_id INT DEFAULT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, INDEX IDX_42C84955FB88E14F (utilisateur_id), INDEX IDX_42C84955806F0F5C (equipement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE seance (id INT AUTO_INCREMENT NOT NULL, type_seance_id INT DEFAULT NULL, date DATETIME NOT NULL, nom VARCHAR(30) NOT NULL, capacite_max INT NOT NULL, salle VARCHAR(255) NOT NULL, statut VARCHAR(25) NOT NULL, objectif VARCHAR(255) NOT NULL, participantsinscrits INT NOT NULL, nom_coach VARCHAR(255) NOT NULL, INDEX IDX_DF7DFD0E57BF3B84 (type_seance_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_seance (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_utilisateur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE typeabonnement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, type_utilisateur_id INT DEFAULT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, age INT NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, genre VARCHAR(50) NOT NULL, email VARCHAR(50) NOT NULL, statut VARCHAR(50) NOT NULL, is_verified TINYINT(1) NOT NULL, reset_token VARCHAR(255) DEFAULT NULL, role VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), INDEX IDX_1D1C63B3AD4BC8DB (type_utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE abonnement ADD CONSTRAINT FK_351268BB813AF326 FOREIGN KEY (type_abonnement_id) REFERENCES typeabonnement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE abonnementachat ADD CONSTRAINT FK_2BFD717F1D74413 FOREIGN KEY (abonnement_id) REFERENCES abonnement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipement ADD CONSTRAINT FK_B8B4C6F3806F0F5C FOREIGN KEY (equipement_id) REFERENCES categorie_equipement (id)');
        $this->addSql('ALTER TABLE equipement_history ADD CONSTRAINT FK_2C373A98806F0F5C FOREIGN KEY (equipement_id) REFERENCES equipement (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955806F0F5C FOREIGN KEY (equipement_id) REFERENCES equipement (id)');
        $this->addSql('ALTER TABLE seance ADD CONSTRAINT FK_DF7DFD0E57BF3B84 FOREIGN KEY (type_seance_id) REFERENCES type_seance (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3AD4BC8DB FOREIGN KEY (type_utilisateur_id) REFERENCES type_utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonnement DROP FOREIGN KEY FK_351268BB813AF326');
        $this->addSql('ALTER TABLE abonnementachat DROP FOREIGN KEY FK_2BFD717F1D74413');
        $this->addSql('ALTER TABLE equipement DROP FOREIGN KEY FK_B8B4C6F3806F0F5C');
        $this->addSql('ALTER TABLE equipement_history DROP FOREIGN KEY FK_2C373A98806F0F5C');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955FB88E14F');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955806F0F5C');
        $this->addSql('ALTER TABLE seance DROP FOREIGN KEY FK_DF7DFD0E57BF3B84');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3AD4BC8DB');
        $this->addSql('DROP TABLE abonnement');
        $this->addSql('DROP TABLE abonnementachat');
        $this->addSql('DROP TABLE categorie_equipement');
        $this->addSql('DROP TABLE equipement');
        $this->addSql('DROP TABLE equipement_history');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE seance');
        $this->addSql('DROP TABLE type_seance');
        $this->addSql('DROP TABLE type_utilisateur');
        $this->addSql('DROP TABLE typeabonnement');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
