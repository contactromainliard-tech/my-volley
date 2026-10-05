<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261005152144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CC5237001 FOREIGN KEY (winner_team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CE6E88A32 FOREIGN KEY (service_team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE game_player ADD CONSTRAINT FK_E52CD7ADE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game_player ADD CONSTRAINT FK_E52CD7AD99E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game_player ADD CONSTRAINT FK_E52CD7AD296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE manche ADD starting_service_team_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE manche ADD CONSTRAINT FK_A06E62EBE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE manche ADD CONSTRAINT FK_A06E62EBC5237001 FOREIGN KEY (winner_team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE manche ADD CONSTRAINT FK_A06E62EB2DB6E466 FOREIGN KEY (starting_service_team_id) REFERENCES team (id)');
        $this->addSql('CREATE INDEX IDX_A06E62EB2DB6E466 ON manche (starting_service_team_id)');
        $this->addSql('ALTER TABLE point ADD CONSTRAINT FK_B7A5F3243E37BFAB FOREIGN KEY (manche_id) REFERENCES manche (id)');
        $this->addSql('ALTER TABLE point ADD CONSTRAINT FK_B7A5F324296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE point ADD CONSTRAINT FK_B7A5F32499E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61FE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CC5237001');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CE6E88A32');
        $this->addSql('ALTER TABLE game_player DROP FOREIGN KEY FK_E52CD7ADE48FD905');
        $this->addSql('ALTER TABLE game_player DROP FOREIGN KEY FK_E52CD7AD99E6F5DF');
        $this->addSql('ALTER TABLE game_player DROP FOREIGN KEY FK_E52CD7AD296CD8AE');
        $this->addSql('ALTER TABLE manche DROP FOREIGN KEY FK_A06E62EBE48FD905');
        $this->addSql('ALTER TABLE manche DROP FOREIGN KEY FK_A06E62EBC5237001');
        $this->addSql('ALTER TABLE manche DROP FOREIGN KEY FK_A06E62EB2DB6E466');
        $this->addSql('DROP INDEX IDX_A06E62EB2DB6E466 ON manche');
        $this->addSql('ALTER TABLE manche DROP starting_service_team_id');
        $this->addSql('ALTER TABLE point DROP FOREIGN KEY FK_B7A5F3243E37BFAB');
        $this->addSql('ALTER TABLE point DROP FOREIGN KEY FK_B7A5F324296CD8AE');
        $this->addSql('ALTER TABLE point DROP FOREIGN KEY FK_B7A5F32499E6F5DF');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61FE48FD905');
    }
}
