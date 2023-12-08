<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231208093429 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire ADD auteur_id INT NOT NULL, ADD recette_id INT NOT NULL');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC89312FE9 FOREIGN KEY (recette_id) REFERENCES recette (id)');
        $this->addSql('CREATE INDEX IDX_67F068BC60BB6FE6 ON commentaire (auteur_id)');
        $this->addSql('CREATE INDEX IDX_67F068BC89312FE9 ON commentaire (recette_id)');
        $this->addSql('ALTER TABLE recette ADD createur_id INT NOT NULL');
        $this->addSql('ALTER TABLE recette ADD CONSTRAINT FK_49BB639073A201E5 FOREIGN KEY (createur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_49BB639073A201E5 ON recette (createur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC60BB6FE6');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC89312FE9');
        $this->addSql('DROP INDEX IDX_67F068BC60BB6FE6 ON commentaire');
        $this->addSql('DROP INDEX IDX_67F068BC89312FE9 ON commentaire');
        $this->addSql('ALTER TABLE commentaire DROP auteur_id, DROP recette_id');
        $this->addSql('ALTER TABLE recette DROP FOREIGN KEY FK_49BB639073A201E5');
        $this->addSql('DROP INDEX IDX_49BB639073A201E5 ON recette');
        $this->addSql('ALTER TABLE recette DROP createur_id');
    }
}
