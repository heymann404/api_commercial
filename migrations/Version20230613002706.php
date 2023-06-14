<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230613002706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE note_de_frais (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, societe_id INT NOT NULL, user_id INT NOT NULL, date_de_la_note DATE NOT NULL, montant DOUBLE PRECISION NOT NULL, date_de_creation DATETIME NOT NULL, date_de_modification DATETIME NOT NULL, INDEX IDX_E6ECCF53C54C8C93 (type_id), INDEX IDX_E6ECCF53FCF77503 (societe_id), INDEX IDX_E6ECCF53A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE societe (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_de_note (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, date_de_naissance DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE note_de_frais ADD CONSTRAINT FK_E6ECCF53C54C8C93 FOREIGN KEY (type_id) REFERENCES type_de_note (id)');
        $this->addSql('ALTER TABLE note_de_frais ADD CONSTRAINT FK_E6ECCF53FCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
        $this->addSql('ALTER TABLE note_de_frais ADD CONSTRAINT FK_E6ECCF53A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE note_de_frais DROP FOREIGN KEY FK_E6ECCF53C54C8C93');
        $this->addSql('ALTER TABLE note_de_frais DROP FOREIGN KEY FK_E6ECCF53FCF77503');
        $this->addSql('ALTER TABLE note_de_frais DROP FOREIGN KEY FK_E6ECCF53A76ED395');
        $this->addSql('DROP TABLE note_de_frais');
        $this->addSql('DROP TABLE societe');
        $this->addSql('DROP TABLE type_de_note');
        $this->addSql('DROP TABLE user');
    }
}
