<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230425230455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, produit_id INT DEFAULT NULL, titre VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, date DATETIME DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, response VARCHAR(255) DEFAULT NULL, INDEX IDX_CE606404F347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404F347EFB FOREIGN KEY (produit_id) REFERENCES produits (id)');
        $this->addSql('ALTER TABLE produits ADD image VARCHAR(255) DEFAULT NULL, ADD genre VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404F347EFB');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('ALTER TABLE produits DROP image, DROP genre');
    }
}
