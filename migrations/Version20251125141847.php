<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251125141847 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pathologie (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE conference ADD pathologie_id INT NOT NULL, ADD medecin_id INT NOT NULL');
        $this->addSql('ALTER TABLE conference ADD CONSTRAINT FK_911533C8E7F789D4 FOREIGN KEY (pathologie_id) REFERENCES pathologie (id)');
        $this->addSql('ALTER TABLE conference ADD CONSTRAINT FK_911533C84F31A84 FOREIGN KEY (medecin_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_911533C8E7F789D4 ON conference (pathologie_id)');
        $this->addSql('CREATE INDEX IDX_911533C84F31A84 ON conference (medecin_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE pathologie');
        $this->addSql('ALTER TABLE conference DROP FOREIGN KEY FK_911533C8E7F789D4');
        $this->addSql('ALTER TABLE conference DROP FOREIGN KEY FK_911533C84F31A84');
        $this->addSql('DROP INDEX IDX_911533C8E7F789D4 ON conference');
        $this->addSql('DROP INDEX IDX_911533C84F31A84 ON conference');
        $this->addSql('ALTER TABLE conference DROP pathologie_id, DROP medecin_id');
    }
}
