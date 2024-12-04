<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241202201841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, total DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cart_produit (cart_id INT NOT NULL, produit_id INT NOT NULL, INDEX IDX_D27F24201AD5CDBF (cart_id), INDEX IDX_D27F2420F347EFB (produit_id), PRIMARY KEY(cart_id, produit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, total DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', produits JSON NOT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_produit (tag_id INT NOT NULL, produit_id INT NOT NULL, INDEX IDX_1B94C18DBAD26311 (tag_id), INDEX IDX_1B94C18DF347EFB (produit_id), PRIMARY KEY(tag_id, produit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart_produit ADD CONSTRAINT FK_D27F24201AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cart_produit ADD CONSTRAINT FK_D27F2420F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_produit ADD CONSTRAINT FK_1B94C18DBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_produit ADD CONSTRAINT FK_1B94C18DF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produit CHANGE image image VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_produit DROP FOREIGN KEY FK_D27F24201AD5CDBF');
        $this->addSql('ALTER TABLE cart_produit DROP FOREIGN KEY FK_D27F2420F347EFB');
        $this->addSql('ALTER TABLE tag_produit DROP FOREIGN KEY FK_1B94C18DBAD26311');
        $this->addSql('ALTER TABLE tag_produit DROP FOREIGN KEY FK_1B94C18DF347EFB');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE cart_produit');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_produit');
        $this->addSql('ALTER TABLE produit CHANGE image image VARCHAR(255) NOT NULL');
    }
}
