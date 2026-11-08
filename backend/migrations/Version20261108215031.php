<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261108215031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE refresh_tokens (
              refresh_token VARCHAR(128) NOT NULL,
              username VARCHAR(255) NOT NULL,
              valid DATETIME NOT NULL,
              id INT AUTO_INCREMENT NOT NULL,
              UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token),
              PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql('ALTER TABLE refresh_token DROP FOREIGN KEY `FK_REFRESH_TOKEN_USER`');
        $this->addSql('DROP TABLE refresh_token');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE refresh_token (
              id INT AUTO_INCREMENT NOT NULL,
              user_id INT NOT NULL,
              token_hash VARCHAR(64) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`,
              expires_at DATETIME NOT NULL,
              UNIQUE INDEX UNIQ_REFRESH_TOKEN_HASH (token_hash),
              INDEX IDX_C74F2195A76ED395 (user_id),
              PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = ''
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              refresh_token
            ADD
              CONSTRAINT `FK_REFRESH_TOKEN_USER` FOREIGN KEY (user_id) REFERENCES user (id) ON
            UPDATE
              NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql('DROP TABLE refresh_tokens');
    }
}
