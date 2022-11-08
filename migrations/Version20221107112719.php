<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221107112719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE delegation (id INT AUTO_INCREMENT NOT NULL, validator_id INT DEFAULT NULL, delegator_address VARCHAR(255) NOT NULL, shares VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_292F436DE9BEE485 (delegator_address), INDEX IDX_292F436DB0644AEC (validator_id), INDEX delegator_address (delegator_address), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE export_process (id INT AUTO_INCREMENT NOT NULL, network VARCHAR(255) NOT NULL, height VARCHAR(255) NOT NULL, is_completed TINYINT(1) DEFAULT 0 NOT NULL, completed_at DATETIME NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE validator (id INT AUTO_INCREMENT NOT NULL, export_id INT DEFAULT NULL, validator_name VARCHAR(255) NOT NULL, validator_address VARCHAR(255) NOT NULL, is_completed TINYINT(1) DEFAULT 0 NOT NULL, completed_at DATETIME DEFAULT NULL, INDEX IDX_FAA290C864CDAF82 (export_id), INDEX validator_address (validator_address), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE delegation ADD CONSTRAINT FK_292F436DB0644AEC FOREIGN KEY (validator_id) REFERENCES validator (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE validator ADD CONSTRAINT FK_FAA290C864CDAF82 FOREIGN KEY (export_id) REFERENCES export_process (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE export_delegations_request ADD export_id INT DEFAULT NULL, DROP download_link');
        $this->addSql('ALTER TABLE export_delegations_request ADD CONSTRAINT FK_1A62974264CDAF82 FOREIGN KEY (export_id) REFERENCES export_process (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_1A62974264CDAF82 ON export_delegations_request (export_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE export_delegations_request DROP FOREIGN KEY FK_1A62974264CDAF82');
        $this->addSql('ALTER TABLE delegation DROP FOREIGN KEY FK_292F436DB0644AEC');
        $this->addSql('ALTER TABLE validator DROP FOREIGN KEY FK_FAA290C864CDAF82');
        $this->addSql('DROP TABLE delegation');
        $this->addSql('DROP TABLE export_process');
        $this->addSql('DROP TABLE validator');
        $this->addSql('DROP INDEX IDX_1A62974264CDAF82 ON export_delegations_request');
        $this->addSql('ALTER TABLE export_delegations_request ADD download_link VARCHAR(255) DEFAULT NULL, DROP export_id');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
