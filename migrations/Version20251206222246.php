<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251206222246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chauffeur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, cin VARCHAR(8) NOT NULL, telephone VARCHAR(15) NOT NULL, email VARCHAR(180) NOT NULL, numero_permis VARCHAR(50) NOT NULL, type_permis VARCHAR(20) NOT NULL, date_delivrance_permis DATE NOT NULL, date_expiration_permis DATE NOT NULL, date_naissance DATE NOT NULL, adresse VARCHAR(255) NOT NULL, ville VARCHAR(100) NOT NULL, statut VARCHAR(20) NOT NULL, date_creation DATETIME NOT NULL, date_modification DATETIME DEFAULT NULL, notes LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_5CA777B8ABE530DA (cin), UNIQUE INDEX UNIQ_5CA777B8E7927C74 (email), UNIQUE INDEX UNIQ_5CA777B84FFF8769 (numero_permis), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE adresse adresse VARCHAR(255) DEFAULT NULL, CHANGE telephone telephone VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE chauffeur');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE `user` CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE adresse adresse VARCHAR(255) DEFAULT \'NULL\', CHANGE telephone telephone VARCHAR(20) DEFAULT \'NULL\'');
    }
}
