<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250707140855 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Modification de la table trello_session : ajout des relations avec les villes de départ/arrivée et correction du type du champ de temps estimé';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_session ADD start_city_id BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)', ADD arrived_city_id BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)', ADD estimated_time TIME NOT NULL, DROP estimed_time
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_session ADD CONSTRAINT FK_9BC0D2FA7B693E7C FOREIGN KEY (start_city_id) REFERENCES trello_city (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_session ADD CONSTRAINT FK_9BC0D2FAF254AEC5 FOREIGN KEY (arrived_city_id) REFERENCES trello_city (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_City_Session_start ON trello_session (start_city_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_City_Session_arrived ON trello_session (arrived_city_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_session DROP FOREIGN KEY FK_9BC0D2FA7B693E7C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_session DROP FOREIGN KEY FK_9BC0D2FAF254AEC5
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_City_Session_start ON trello_session
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_City_Session_arrived ON trello_session
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_session ADD estimed_time DATETIME NOT NULL, DROP start_city_id, DROP arrived_city_id, DROP estimated_time
        SQL);
    }
}
