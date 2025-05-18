<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250518172808 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE trello_booking (id BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)', user_id BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)', session_id BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)', reservation_date DATE NOT NULL, price DOUBLE PRECISION NOT NULL, INDEX IDX_User_Booking (user_id), INDEX IDX_Session_Booking (session_id), UNIQUE INDEX UNIQ_Booking_User_Session (user_id, session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trello_bus (id BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)', name VARCHAR(50) NOT NULL, immatriculation VARCHAR(20) NOT NULL, model VARCHAR(50) NOT NULL, capacity INT NOT NULL, UNIQUE INDEX UQ_Bus_Immatriculation (immatriculation), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trello_city (id BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)', name VARCHAR(50) NOT NULL, pays VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_City_Name (name, pays), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trello_road (id BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)', start_city_id BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)', arrived_city_id BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)', estimated_time TIME NOT NULL, INDEX IDX_City_Road_Start (start_city_id), INDEX IDX_City_Road_Arrived (arrived_city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trello_role (id BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)', name VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_Role_Name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trello_seat (id BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)', session_id BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)', booking_id BINARY(16) DEFAULT NULL COMMENT '(DC2Type:ulid)', number INT NOT NULL, state VARCHAR(255) NOT NULL, INDEX IDX_Session_Seat (session_id), INDEX IDX_Booking_Seat (booking_id), UNIQUE INDEX UNIQ_Seat_Number_Session (number, session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trello_session (id BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)', road_id BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)', bus_id BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)', unit_price DOUBLE PRECISION NOT NULL, departure_date DATETIME NOT NULL, INDEX IDX_Road_Session (road_id), INDEX IDX_Bus_Session (bus_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trello_user (id BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)', role_id BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)', last_name VARCHAR(20) NOT NULL, first_name VARCHAR(20) NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(50) NOT NULL, INDEX IDX_User_Role (role_id), UNIQUE INDEX UNIQ_User_Email (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_booking ADD CONSTRAINT FK_AB88EAF0A76ED395 FOREIGN KEY (user_id) REFERENCES trello_user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_booking ADD CONSTRAINT FK_AB88EAF0613FECDF FOREIGN KEY (session_id) REFERENCES trello_session (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_road ADD CONSTRAINT FK_FD036C8E7B693E7C FOREIGN KEY (start_city_id) REFERENCES trello_city (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_road ADD CONSTRAINT FK_FD036C8EF254AEC5 FOREIGN KEY (arrived_city_id) REFERENCES trello_city (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_seat ADD CONSTRAINT FK_559F9E59613FECDF FOREIGN KEY (session_id) REFERENCES trello_session (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_seat ADD CONSTRAINT FK_559F9E593301C60 FOREIGN KEY (booking_id) REFERENCES trello_booking (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_session ADD CONSTRAINT FK_9BC0D2FA962F8178 FOREIGN KEY (road_id) REFERENCES trello_road (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_session ADD CONSTRAINT FK_9BC0D2FA2546731D FOREIGN KEY (bus_id) REFERENCES trello_bus (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_user ADD CONSTRAINT FK_E5507E76D60322AC FOREIGN KEY (role_id) REFERENCES trello_role (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_booking DROP FOREIGN KEY FK_AB88EAF0A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_booking DROP FOREIGN KEY FK_AB88EAF0613FECDF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_road DROP FOREIGN KEY FK_FD036C8E7B693E7C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_road DROP FOREIGN KEY FK_FD036C8EF254AEC5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_seat DROP FOREIGN KEY FK_559F9E59613FECDF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_seat DROP FOREIGN KEY FK_559F9E593301C60
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_session DROP FOREIGN KEY FK_9BC0D2FA962F8178
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_session DROP FOREIGN KEY FK_9BC0D2FA2546731D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trello_user DROP FOREIGN KEY FK_E5507E76D60322AC
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trello_booking
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trello_bus
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trello_city
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trello_road
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trello_role
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trello_seat
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trello_session
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trello_user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
