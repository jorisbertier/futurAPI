<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230726180915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nft ADD eth_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE nft ADD CONSTRAINT FK_D9C7463C823BBA8B FOREIGN KEY (eth_id) REFERENCES eth (id)');
        $this->addSql('CREATE INDEX IDX_D9C7463C823BBA8B ON nft (eth_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nft DROP FOREIGN KEY FK_D9C7463C823BBA8B');
        $this->addSql('DROP INDEX IDX_D9C7463C823BBA8B ON nft');
        $this->addSql('ALTER TABLE nft DROP eth_id');
    }
}
