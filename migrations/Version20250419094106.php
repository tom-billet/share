<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250419094106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, topic VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL, message LONGTEXT NOT NULL, sending_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, original_name VARCHAR(255) NOT NULL, server_name VARCHAR(255) NOT NULL, sending_date DATETIME NOT NULL, extension VARCHAR(5) NOT NULL, size INT NOT NULL, INDEX IDX_8C9F3610A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file_subcategory (file_id INT NOT NULL, subcategory_id INT NOT NULL, INDEX IDX_2F675A2A93CB796C (file_id), INDEX IDX_2F675A2A5DC6FE57 (subcategory_id), PRIMARY KEY(file_id, subcategory_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file_share (file_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_45852D6D93CB796C (file_id), INDEX IDX_45852D6DA76ED395 (user_id), PRIMARY KEY(file_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subcategory (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, label VARCHAR(50) NOT NULL, number INT NOT NULL, INDEX IDX_DDCA44812469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, registration_date DATETIME NOT NULL, name VARCHAR(50) NOT NULL, surname VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_ask (user_source INT NOT NULL, user_target INT NOT NULL, INDEX IDX_83308B6B3AD8644E (user_source), INDEX IDX_83308B6B233D34C1 (user_target), PRIMARY KEY(user_source, user_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_accept (user_id INT NOT NULL, accept_id INT NOT NULL, INDEX IDX_D672F609A76ED395 (user_id), INDEX IDX_D672F609959D25A6 (accept_id), PRIMARY KEY(user_id, accept_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE file_subcategory ADD CONSTRAINT FK_2F675A2A93CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE file_subcategory ADD CONSTRAINT FK_2F675A2A5DC6FE57 FOREIGN KEY (subcategory_id) REFERENCES subcategory (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE file_share ADD CONSTRAINT FK_45852D6D93CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE file_share ADD CONSTRAINT FK_45852D6DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subcategory ADD CONSTRAINT FK_DDCA44812469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE user_ask ADD CONSTRAINT FK_83308B6B3AD8644E FOREIGN KEY (user_source) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_ask ADD CONSTRAINT FK_83308B6B233D34C1 FOREIGN KEY (user_target) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_accept ADD CONSTRAINT FK_D672F609A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_accept ADD CONSTRAINT FK_D672F609959D25A6 FOREIGN KEY (accept_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610A76ED395');
        $this->addSql('ALTER TABLE file_subcategory DROP FOREIGN KEY FK_2F675A2A93CB796C');
        $this->addSql('ALTER TABLE file_subcategory DROP FOREIGN KEY FK_2F675A2A5DC6FE57');
        $this->addSql('ALTER TABLE file_share DROP FOREIGN KEY FK_45852D6D93CB796C');
        $this->addSql('ALTER TABLE file_share DROP FOREIGN KEY FK_45852D6DA76ED395');
        $this->addSql('ALTER TABLE subcategory DROP FOREIGN KEY FK_DDCA44812469DE2');
        $this->addSql('ALTER TABLE user_ask DROP FOREIGN KEY FK_83308B6B3AD8644E');
        $this->addSql('ALTER TABLE user_ask DROP FOREIGN KEY FK_83308B6B233D34C1');
        $this->addSql('ALTER TABLE user_accept DROP FOREIGN KEY FK_D672F609A76ED395');
        $this->addSql('ALTER TABLE user_accept DROP FOREIGN KEY FK_D672F609959D25A6');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE file_subcategory');
        $this->addSql('DROP TABLE file_share');
        $this->addSql('DROP TABLE subcategory');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_ask');
        $this->addSql('DROP TABLE user_accept');
    }
}
