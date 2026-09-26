<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260926122739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_232B318CEADBA746 ON game');
        $this->addSql('ALTER TABLE game CHANGE winner_team_id_id winner_team_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CC5237001 FOREIGN KEY (winner_team_id) REFERENCES team (id)');
        $this->addSql('CREATE INDEX IDX_232B318CC5237001 ON game (winner_team_id)');
        $this->addSql('ALTER TABLE game_player ADD CONSTRAINT FK_E52CD7ADE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game_player ADD CONSTRAINT FK_E52CD7AD99E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game_player ADD CONSTRAINT FK_E52CD7AD296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('DROP INDEX IDX_E61425DCEADBA746 ON manche');
        $this->addSql('ALTER TABLE manche CHANGE winnerTeam winner_team_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE manche ADD CONSTRAINT FK_A06E62EBE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE manche ADD CONSTRAINT FK_A06E62EBC5237001 FOREIGN KEY (winner_team_id) REFERENCES team (id)');
        $this->addSql('CREATE INDEX IDX_A06E62EBC5237001 ON manche (winner_team_id)');
        $this->addSql('ALTER TABLE manche RENAME INDEX idx_e61425dce48fd905 TO IDX_A06E62EBE48FD905');
        $this->addSql('DROP INDEX IDX_B7A5F32410FB0D18 ON point');
        $this->addSql('ALTER TABLE point CHANGE set_id manche_id INT NOT NULL');
        $this->addSql('ALTER TABLE point ADD CONSTRAINT FK_B7A5F3243E37BFAB FOREIGN KEY (manche_id) REFERENCES manche (id)');
        $this->addSql('ALTER TABLE point ADD CONSTRAINT FK_B7A5F324296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE point ADD CONSTRAINT FK_B7A5F32499E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('CREATE INDEX IDX_B7A5F3243E37BFAB ON point (manche_id)');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61FE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CC5237001');
        $this->addSql('DROP INDEX IDX_232B318CC5237001 ON game');
        $this->addSql('ALTER TABLE game CHANGE winner_team_id winner_team_id_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_232B318CEADBA746 ON game (winner_team_id_id)');
        $this->addSql('ALTER TABLE game_player DROP FOREIGN KEY FK_E52CD7ADE48FD905');
        $this->addSql('ALTER TABLE game_player DROP FOREIGN KEY FK_E52CD7AD99E6F5DF');
        $this->addSql('ALTER TABLE game_player DROP FOREIGN KEY FK_E52CD7AD296CD8AE');
        $this->addSql('ALTER TABLE manche DROP FOREIGN KEY FK_A06E62EBE48FD905');
        $this->addSql('ALTER TABLE manche DROP FOREIGN KEY FK_A06E62EBC5237001');
        $this->addSql('DROP INDEX IDX_A06E62EBC5237001 ON manche');
        $this->addSql('ALTER TABLE manche CHANGE winner_team_id winnerTeam INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_E61425DCEADBA746 ON manche (winnerTeam)');
        $this->addSql('ALTER TABLE manche RENAME INDEX idx_a06e62ebe48fd905 TO IDX_E61425DCE48FD905');
        $this->addSql('ALTER TABLE point DROP FOREIGN KEY FK_B7A5F3243E37BFAB');
        $this->addSql('ALTER TABLE point DROP FOREIGN KEY FK_B7A5F324296CD8AE');
        $this->addSql('ALTER TABLE point DROP FOREIGN KEY FK_B7A5F32499E6F5DF');
        $this->addSql('DROP INDEX IDX_B7A5F3243E37BFAB ON point');
        $this->addSql('ALTER TABLE point CHANGE manche_id set_id INT NOT NULL');
        $this->addSql('CREATE INDEX IDX_B7A5F32410FB0D18 ON point (set_id)');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61FE48FD905');
    }
}
