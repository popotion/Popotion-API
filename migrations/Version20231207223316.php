<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231207223316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favoris ADD auteur_id INT NOT NULL, ADD recette_id INT NOT NULL');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C43260BB6FE6 FOREIGN KEY (auteur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C43289312FE9 FOREIGN KEY (recette_id) REFERENCES recette (id)');
        $this->addSql('CREATE INDEX IDX_8933C43260BB6FE6 ON favoris (auteur_id)');
        $this->addSql('CREATE INDEX IDX_8933C43289312FE9 ON favoris (recette_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C43260BB6FE6');
        $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C43289312FE9');
        $this->addSql('DROP INDEX IDX_8933C43260BB6FE6 ON favoris');
        $this->addSql('DROP INDEX IDX_8933C43289312FE9 ON favoris');
        $this->addSql('ALTER TABLE favoris DROP auteur_id, DROP recette_id');
    }
}
