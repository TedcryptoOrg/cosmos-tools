<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230828151510 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Introduce a fee grant wallet table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE fee_grant_wallet (
                    id INT AUTO_INCREMENT NOT NULL, 
                    is_enabled TINYINT(1) NOT NULL, 
                    address VARCHAR(255) NOT NULL, 
                    mnemonic VARCHAR(255) NOT NULL, 
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE fee_grant_wallet');
    }
}
