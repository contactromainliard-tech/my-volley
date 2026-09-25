<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260925141503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, status VARCHAR(255) NOT NULL, winner_team_id_id INT DEFAULT NULL, INDEX IDX_232B318CEADBA746 (winner_team_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE game_player (id INT AUTO_INCREMENT NOT NULL, is_on_court TINYINT NOT NULL, game_id INT DEFAULT NULL, player_id INT NOT NULL, team_id INT DEFAULT NULL, INDEX IDX_E52CD7ADE48FD905 (game_id), INDEX IDX_E52CD7AD99E6F5DF (player_id), INDEX IDX_E52CD7AD296CD8AE (team_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE player (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE point (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, sequence_number INT NOT NULL, is_cancelled TINYINT NOT NULL, cancelled_at DATETIME NOT NULL, created_at DATETIME NOT NULL, set_id INT NOT NULL, team_id INT NOT NULL, player_id INT NOT NULL, INDEX IDX_B7A5F32410FB0D18 (set_id), INDEX IDX_B7A5F324296CD8AE (team_id), INDEX IDX_B7A5F32499E6F5DF (player_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE `set` (id INT AUTO_INCREMENT NOT NULL, number INT NOT NULL, score_team1 INT NOT NULL, score_team2 INT NOT NULL, status VARCHAR(255) NOT NULL, game_id INT NOT NULL, winner_team_id_id INT DEFAULT NULL, INDEX IDX_E61425DCE48FD905 (game_id), INDEX IDX_E61425DCEADBA746 (winner_team_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, game_id INT NOT NULL, INDEX IDX_C4E0A61FE48FD905 (game_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CEADBA746 FOREIGN KEY (winner_team_id_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE game_player ADD CONSTRAINT FK_E52CD7ADE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game_player ADD CONSTRAINT FK_E52CD7AD99E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game_player ADD CONSTRAINT FK_E52CD7AD296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE point ADD CONSTRAINT FK_B7A5F32410FB0D18 FOREIGN KEY (set_id) REFERENCES `set` (id)');
        $this->addSql('ALTER TABLE point ADD CONSTRAINT FK_B7A5F324296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE point ADD CONSTRAINT FK_B7A5F32499E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE `set` ADD CONSTRAINT FK_E61425DCE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE `set` ADD CONSTRAINT FK_E61425DCEADBA746 FOREIGN KEY (winner_team_id_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61FE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CEADBA746');
        $this->addSql('ALTER TABLE game_player DROP FOREIGN KEY FK_E52CD7ADE48FD905');
        $this->addSql('ALTER TABLE game_player DROP FOREIGN KEY FK_E52CD7AD99E6F5DF');
        $this->addSql('ALTER TABLE game_player DROP FOREIGN KEY FK_E52CD7AD296CD8AE');
        $this->addSql('ALTER TABLE point DROP FOREIGN KEY FK_B7A5F32410FB0D18');
        $this->addSql('ALTER TABLE point DROP FOREIGN KEY FK_B7A5F324296CD8AE');
        $this->addSql('ALTER TABLE point DROP FOREIGN KEY FK_B7A5F32499E6F5DF');
        $this->addSql('ALTER TABLE `set` DROP FOREIGN KEY FK_E61425DCE48FD905');
        $this->addSql('ALTER TABLE `set` DROP FOREIGN KEY FK_E61425DCEADBA746');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61FE48FD905');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_player');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE point');
        $this->addSql('DROP TABLE `set`');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
