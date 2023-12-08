<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231208171402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE recette_categorie (recette_id INT NOT NULL, categorie_id INT NOT NULL, INDEX IDX_FAABB8FA89312FE9 (recette_id), INDEX IDX_FAABB8FABCF5E72D (categorie_id), PRIMARY KEY(recette_id, categorie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recette_ingredient (recette_id INT NOT NULL, ingredient_id INT NOT NULL, INDEX IDX_17C041A989312FE9 (recette_id), INDEX IDX_17C041A9933FE08C (ingredient_id), PRIMARY KEY(recette_id, ingredient_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE recette_categorie ADD CONSTRAINT FK_FAABB8FA89312FE9 FOREIGN KEY (recette_id) REFERENCES recette (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recette_categorie ADD CONSTRAINT FK_FAABB8FABCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recette_ingredient ADD CONSTRAINT FK_17C041A989312FE9 FOREIGN KEY (recette_id) REFERENCES recette (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recette_ingredient ADD CONSTRAINT FK_17C041A9933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commentaire ADD recette_id INT NOT NULL, ADD auteur_id INT NOT NULL');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC89312FE9 FOREIGN KEY (recette_id) REFERENCES recette (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_67F068BC89312FE9 ON commentaire (recette_id)');
        $this->addSql('CREATE INDEX IDX_67F068BC60BB6FE6 ON commentaire (auteur_id)');
        $this->addSql('ALTER TABLE favoris ADD auteur_id INT NOT NULL, ADD recette_id INT NOT NULL');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C43260BB6FE6 FOREIGN KEY (auteur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C43289312FE9 FOREIGN KEY (recette_id) REFERENCES recette (id)');
        $this->addSql('CREATE INDEX IDX_8933C43260BB6FE6 ON favoris (auteur_id)');
        $this->addSql('CREATE INDEX IDX_8933C43289312FE9 ON favoris (recette_id)');
        $this->addSql('ALTER TABLE recette ADD createur_id INT NOT NULL');
        $this->addSql('ALTER TABLE recette ADD CONSTRAINT FK_49BB639073A201E5 FOREIGN KEY (createur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_49BB639073A201E5 ON recette (createur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recette_categorie DROP FOREIGN KEY FK_FAABB8FA89312FE9');
        $this->addSql('ALTER TABLE recette_categorie DROP FOREIGN KEY FK_FAABB8FABCF5E72D');
        $this->addSql('ALTER TABLE recette_ingredient DROP FOREIGN KEY FK_17C041A989312FE9');
        $this->addSql('ALTER TABLE recette_ingredient DROP FOREIGN KEY FK_17C041A9933FE08C');
        $this->addSql('DROP TABLE recette_categorie');
        $this->addSql('DROP TABLE recette_ingredient');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC89312FE9');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC60BB6FE6');
        $this->addSql('DROP INDEX IDX_67F068BC89312FE9 ON commentaire');
        $this->addSql('DROP INDEX IDX_67F068BC60BB6FE6 ON commentaire');
        $this->addSql('ALTER TABLE commentaire DROP recette_id, DROP auteur_id');
        $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C43260BB6FE6');
        $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C43289312FE9');
        $this->addSql('DROP INDEX IDX_8933C43260BB6FE6 ON favoris');
        $this->addSql('DROP INDEX IDX_8933C43289312FE9 ON favoris');
        $this->addSql('ALTER TABLE favoris DROP auteur_id, DROP recette_id');
        $this->addSql('ALTER TABLE recette DROP FOREIGN KEY FK_49BB639073A201E5');
        $this->addSql('DROP INDEX IDX_49BB639073A201E5 ON recette');
        $this->addSql('ALTER TABLE recette DROP createur_id');
    }
}
