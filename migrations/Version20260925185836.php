<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260925185836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CEADBA746 FOREIGN KEY (winner_team_id_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE game_player ADD CONSTRAINT FK_E52CD7ADE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game_player ADD CONSTRAINT FK_E52CD7AD99E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game_player ADD CONSTRAINT FK_E52CD7AD296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE point CHANGE cancelled_at cancelled_at DATETIME DEFAULT NULL, CHANGE player_id player_id INT DEFAULT NULL');
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
        $this->addSql('ALTER TABLE point CHANGE cancelled_at cancelled_at DATETIME NOT NULL, CHANGE player_id player_id INT NOT NULL');
        $this->addSql('ALTER TABLE `set` DROP FOREIGN KEY FK_E61425DCE48FD905');
        $this->addSql('ALTER TABLE `set` DROP FOREIGN KEY FK_E61425DCEADBA746');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61FE48FD905');
    }
}
