<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261109190431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Recalcule score_team1 / score_team2 des sets à partir des points non annulés';
    }

    public function up(Schema $schema): void
    {
        // L'équipe 1 d'un match est celle avec le plus petit id (ordre de création)
        $this->addSql(<<<'SQL'
            UPDATE manche m
            SET
                m.score_team1 = (
                    SELECT COUNT(*) FROM point p
                    WHERE p.manche_id = m.id AND p.is_cancelled = 0
                      AND p.team_id = (SELECT MIN(t.id) FROM team t WHERE t.game_id = m.game_id)
                ),
                m.score_team2 = (
                    SELECT COUNT(*) FROM point p
                    WHERE p.manche_id = m.id AND p.is_cancelled = 0
                      AND p.team_id = (SELECT MAX(t.id) FROM team t WHERE t.game_id = m.game_id)
                )
            SQL);
    }

    public function down(Schema $schema): void
    {
        // Données uniquement : rien à annuler
    }
}
