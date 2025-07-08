<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250611130409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Suppression de l\'index unique sur les villes de départ et d\'arrivée dans la table trello_road';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_Road_Start_Arrived ON trello_road
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_Road_Start_Arrived ON trello_road (start_city_id, arrived_city_id)
        SQL);
    }
}
