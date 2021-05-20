<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210520083747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artist (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, lastname VARCHAR(80) DEFAULT NULL, firstname VARCHAR(80) DEFAULT NULL, pseudo VARCHAR(80) NOT NULL, tattoo_shop VARCHAR(100) DEFAULT NULL, city VARCHAR(100) DEFAULT NULL, address VARCHAR(100) DEFAULT NULL, profile_picture VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, instagram VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, siret VARCHAR(14) DEFAULT NULL, hide TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_1599687E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, artist_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_16DB4F89B7970CF8 (artist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE style (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE style_artist (style_id INT NOT NULL, artist_id INT NOT NULL, INDEX IDX_2BFC685DBACD6074 (style_id), INDEX IDX_2BFC685DB7970CF8 (artist_id), PRIMARY KEY(style_id, artist_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id)');
        $this->addSql('ALTER TABLE style_artist ADD CONSTRAINT FK_2BFC685DBACD6074 FOREIGN KEY (style_id) REFERENCES style (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE style_artist ADD CONSTRAINT FK_2BFC685DB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89B7970CF8');
        $this->addSql('ALTER TABLE style_artist DROP FOREIGN KEY FK_2BFC685DB7970CF8');
        $this->addSql('ALTER TABLE style_artist DROP FOREIGN KEY FK_2BFC685DBACD6074');
        $this->addSql('DROP TABLE artist');
        $this->addSql('DROP TABLE picture');
        $this->addSql('DROP TABLE style');
        $this->addSql('DROP TABLE style_artist');
    }
}
