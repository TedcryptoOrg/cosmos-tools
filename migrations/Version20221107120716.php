<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221107120716 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE export_delegations_request CHANGE api_client api_client VARCHAR(255) NOT NULL, CHANGE height height VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE export_delegations_request CHANGE api_client api_client VARCHAR(255) DEFAULT NULL, CHANGE height height VARCHAR(255) DEFAULT NULL');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
