<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230428144755 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE produits');
        $this->addSql('ALTER TABLE reservation CHANGE evenement_nom evenement_nom VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955830BACD7 FOREIGN KEY (evenement_nom) REFERENCES evenement (nom)');
        $this->addSql('CREATE INDEX IDX_42C84955830BACD7 ON reservation (evenement_nom)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE produits (Id INT NOT NULL, nom VARCHAR(255) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, genre VARCHAR(255) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, prix DOUBLE PRECISION NOT NULL, img BLOB DEFAULT NULL, PRIMARY KEY(Id)) DEFAULT CHARACTER SET latin1 COLLATE `latin1_swedish_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955830BACD7');
        $this->addSql('DROP INDEX IDX_42C84955830BACD7 ON reservation');
        $this->addSql('ALTER TABLE reservation CHANGE evenement_nom evenement_nom VARCHAR(255) NOT NULL');
    }
}
