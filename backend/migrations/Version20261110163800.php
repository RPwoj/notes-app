<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261110163800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add pending email for user email confirmation';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD pending_email VARCHAR(180) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP pending_email');
    }
}
