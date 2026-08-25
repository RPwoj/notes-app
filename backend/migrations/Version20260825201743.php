<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260825201743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add an owner to every note';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE note ADD owner_id INT DEFAULT NULL');
        $this->addSql('UPDATE note SET owner_id = (SELECT id FROM (SELECT id FROM user ORDER BY id ASC LIMIT 1) AS first_user) WHERE owner_id IS NULL');
        $this->addSql('ALTER TABLE note MODIFY owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA147E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_CFBDFA147E3C61F9 ON note (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA147E3C61F9');
        $this->addSql('DROP INDEX IDX_CFBDFA147E3C61F9 ON note');
        $this->addSql('ALTER TABLE note DROP owner_id');
    }
}
