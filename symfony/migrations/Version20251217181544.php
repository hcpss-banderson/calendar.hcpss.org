<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251217181544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE feed (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(50) NOT NULL, source VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feed_calendar (feed_id INT NOT NULL, calendar_id INT NOT NULL, INDEX IDX_4E7C36E151A5BC03 (feed_id), INDEX IDX_4E7C36E1A40A2C8 (calendar_id), PRIMARY KEY(feed_id, calendar_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE feed_calendar ADD CONSTRAINT FK_4E7C36E151A5BC03 FOREIGN KEY (feed_id) REFERENCES feed (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE feed_calendar ADD CONSTRAINT FK_4E7C36E1A40A2C8 FOREIGN KEY (calendar_id) REFERENCES calendar (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE feed_calendar DROP FOREIGN KEY FK_4E7C36E151A5BC03');
        $this->addSql('ALTER TABLE feed_calendar DROP FOREIGN KEY FK_4E7C36E1A40A2C8');
        $this->addSql('DROP TABLE feed');
        $this->addSql('DROP TABLE feed_calendar');
    }
}
