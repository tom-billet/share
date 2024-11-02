<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241102203532 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_accept (user_id INT NOT NULL, accept_id INT NOT NULL, INDEX IDX_D672F609A76ED395 (user_id), INDEX IDX_D672F609959D25A6 (accept_id), PRIMARY KEY(user_id, accept_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_accept ADD CONSTRAINT FK_D672F609A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_accept ADD CONSTRAINT FK_D672F609959D25A6 FOREIGN KEY (accept_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_accept DROP FOREIGN KEY FK_D672F609A76ED395');
        $this->addSql('ALTER TABLE user_accept DROP FOREIGN KEY FK_D672F609959D25A6');
        $this->addSql('DROP TABLE user_accept');
    }
}
