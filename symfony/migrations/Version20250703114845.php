<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250703114845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, calendar_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_3BAE0AA7A40A2C8 (calendar_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7A40A2C8 FOREIGN KEY (calendar_id) REFERENCES calendar (id)');
        $this->addSql('ALTER TABLE occurrence DROP FOREIGN KEY FK_BEFD81F3A40A2C8');
        $this->addSql('DROP INDEX IDX_BEFD81F3A40A2C8 ON occurrence');
        $this->addSql('ALTER TABLE occurrence DROP title, DROP description, CHANGE calendar_id event_id INT NOT NULL');
        $this->addSql('ALTER TABLE occurrence ADD CONSTRAINT FK_BEFD81F371F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('CREATE INDEX IDX_BEFD81F371F7E88B ON occurrence (event_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE occurrence DROP FOREIGN KEY FK_BEFD81F371F7E88B');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7A40A2C8');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP INDEX IDX_BEFD81F371F7E88B ON occurrence');
        $this->addSql('ALTER TABLE occurrence ADD title VARCHAR(255) NOT NULL, ADD description LONGTEXT DEFAULT NULL, CHANGE event_id calendar_id INT NOT NULL');
        $this->addSql('ALTER TABLE occurrence ADD CONSTRAINT FK_BEFD81F3A40A2C8 FOREIGN KEY (calendar_id) REFERENCES calendar (id)');
        $this->addSql('CREATE INDEX IDX_BEFD81F3A40A2C8 ON occurrence (calendar_id)');
    }
}
