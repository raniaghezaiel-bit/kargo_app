<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260113192648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chauffeur CHANGE date_modification date_modification DATETIME DEFAULT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE horaire ADD date_depart DATE NOT NULL, ADD places_disponibles INT NOT NULL, CHANGE jours_actifs jours_actifs JSON NOT NULL, CHANGE duree duree VARCHAR(50) DEFAULT NULL, CHANGE date_modification date_modification DATETIME DEFAULT NULL, CHANGE bus_id bus_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation CHANGE qr_code qr_code VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE adresse adresse VARCHAR(255) DEFAULT NULL, CHANGE telephone telephone VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chauffeur CHANGE date_modification date_modification DATETIME DEFAULT \'NULL\', CHANGE photo photo VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE horaire DROP date_depart, DROP places_disponibles, CHANGE jours_actifs jours_actifs LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE duree duree VARCHAR(50) DEFAULT \'NULL\', CHANGE date_modification date_modification DATETIME DEFAULT \'NULL\', CHANGE bus_id bus_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE reservation CHANGE qr_code qr_code VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE `user` CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE adresse adresse VARCHAR(255) DEFAULT \'NULL\', CHANGE telephone telephone VARCHAR(20) DEFAULT \'NULL\'');
    }
}
