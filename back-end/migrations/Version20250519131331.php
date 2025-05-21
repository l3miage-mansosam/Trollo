<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250519131331 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute un index unique sur les colonnes start_city_id et arrived_city_id de la table trello_road pour éviter les doublons de routes entre les mêmes villes';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_Road_Start_Arrived ON trello_road (start_city_id, arrived_city_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_Road_Start_Arrived ON trello_road
        SQL);
    }
}