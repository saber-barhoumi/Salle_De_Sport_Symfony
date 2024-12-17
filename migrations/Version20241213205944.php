<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241213205944 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart_item (id INT AUTO_INCREMENT NOT NULL, cart_id INT NOT NULL, produit_id INT NOT NULL, quantite INT NOT NULL, total DOUBLE PRECISION NOT NULL, INDEX IDX_F0FE25271AD5CDBF (cart_id), INDEX IDX_F0FE2527F347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE25271AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE2527F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE cart_produit DROP FOREIGN KEY FK_D27F24201AD5CDBF');
        $this->addSql('ALTER TABLE cart_produit DROP FOREIGN KEY FK_D27F2420F347EFB');
        $this->addSql('DROP TABLE cart_produit');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7FB88E14F');
        $this->addSql('DROP INDEX UNIQ_BA388B7FB88E14F ON cart');
        $this->addSql('ALTER TABLE cart DROP utilisateur_id, CHANGE session_id session_id VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart_produit (cart_id INT NOT NULL, produit_id INT NOT NULL, INDEX IDX_D27F2420F347EFB (produit_id), INDEX IDX_D27F24201AD5CDBF (cart_id), PRIMARY KEY(cart_id, produit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE cart_produit ADD CONSTRAINT FK_D27F24201AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cart_produit ADD CONSTRAINT FK_D27F2420F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE25271AD5CDBF');
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE2527F347EFB');
        $this->addSql('DROP TABLE cart_item');
        $this->addSql('ALTER TABLE cart ADD utilisateur_id INT DEFAULT NULL, CHANGE session_id session_id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BA388B7FB88E14F ON cart (utilisateur_id)');
    }
}
