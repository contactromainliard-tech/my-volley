<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261123100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Joueurs sans nom des matchs existants : « IC » + leur numéro dans l\'équipe';
    }

    public function up(Schema $schema): void
    {
        // Numéro = rang du joueur dans son équipe (ordre de saisie), sans fonction de fenêtrage
        $this->addSql(<<<'SQL'
            UPDATE player p
            JOIN game_player gp ON gp.player_id = p.id
            SET p.name = CONCAT('IC', (
                SELECT COUNT(*) FROM game_player g2
                WHERE g2.team_id = gp.team_id AND g2.id <= gp.id
            ))
            WHERE TRIM(p.name) = ''
            SQL);
    }

    public function down(Schema $schema): void
    {
        // Données uniquement : les noms d'origine étaient vides, rien à restaurer
    }
}
