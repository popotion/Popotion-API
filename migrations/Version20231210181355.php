<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231210181355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8D93D64981D578D5 ON user');
        $this->addSql('ALTER TABLE user CHANGE login login VARCHAR(180) NOT NULL, CHANGE mail_adress mail_adress VARCHAR(255) NOT NULL, CHANGE status status LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE login login VARCHAR(55) NOT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL, CHANGE mail_adress mail_adress VARCHAR(128) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64981D578D5 ON user (mail_adress)');
    }
}
