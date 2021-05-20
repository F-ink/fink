<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210520080751 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist ADD lastname VARCHAR(80) DEFAULT NULL, ADD firstname VARCHAR(80) DEFAULT NULL, ADD pseudo VARCHAR(80) NOT NULL, ADD tattoo_shop VARCHAR(100) DEFAULT NULL, ADD city VARCHAR(100) DEFAULT NULL, ADD address VARCHAR(100) DEFAULT NULL, ADD profile_picture VARCHAR(255) DEFAULT NULL, ADD description LONGTEXT DEFAULT NULL, ADD instagram VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD siret VARCHAR(14) DEFAULT NULL, ADD hide TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist DROP lastname, DROP firstname, DROP pseudo, DROP tattoo_shop, DROP city, DROP address, DROP profile_picture, DROP description, DROP instagram, DROP created_at, DROP siret, DROP hide');
    }
}
