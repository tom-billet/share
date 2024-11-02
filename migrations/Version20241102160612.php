<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241102160612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE file_subcategory (file_id INT NOT NULL, subcategory_id INT NOT NULL, INDEX IDX_2F675A2A93CB796C (file_id), INDEX IDX_2F675A2A5DC6FE57 (subcategory_id), PRIMARY KEY(file_id, subcategory_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE file_subcategory ADD CONSTRAINT FK_2F675A2A93CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE file_subcategory ADD CONSTRAINT FK_2F675A2A5DC6FE57 FOREIGN KEY (subcategory_id) REFERENCES subcategory (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file_subcategory DROP FOREIGN KEY FK_2F675A2A93CB796C');
        $this->addSql('ALTER TABLE file_subcategory DROP FOREIGN KEY FK_2F675A2A5DC6FE57');
        $this->addSql('DROP TABLE file_subcategory');
    }
}
