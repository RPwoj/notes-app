<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261108010000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Align refresh token schema with Doctrine mapping';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE refresh_token CHANGE expires_at expires_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE refresh_token RENAME INDEX idx_refresh_token_user TO IDX_C74F2195A76ED395');
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE refresh_token CHANGE expires_at expires_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'");
        $this->addSql('ALTER TABLE refresh_token RENAME INDEX IDX_C74F2195A76ED395 TO IDX_REFRESH_TOKEN_USER');
    }
}
