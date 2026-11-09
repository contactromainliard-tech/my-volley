<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Player;
use App\Entity\GamePlayer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GamePlayerController extends AbstractController
{
    #[Route('/game/{gameId}/substitution', name: 'app_game_substitution', methods: ['POST'])]
    public function substitutePlayer(int $gameId, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $teamId      = $data['team_id'] ?? null;
        $playerOutId = $data['player_out_id'] ?? null;
        $playerInId  = $data['player_in_id'] ?? null;

        $game      = $entityManager->getRepository(Game::class)->find($gameId);
        $team      = $entityManager->getRepository(Team::class)->find($teamId);
        $playerOut = $entityManager->getRepository(Player::class)->find($playerOutId);
        $playerIn  = $entityManager->getRepository(Player::class)->find($playerInId);

        if (!$game || !$team || !$playerOut || !$playerIn) {
            return new JsonResponse(['error' => 'Game, Team or Player not found'], 404);
        }

        $gamePlayerOut = $entityManager->getRepository(GamePlayer::class)->findOneBy([
            'game'   => $game,
            'team'   => $team,
            'player' => $playerOut
        ]);

        $gamePlayerIn = $entityManager->getRepository(GamePlayer::class)->findOneBy([
            'game'   => $game,
            'team'   => $team,
            'player' => $playerIn
        ]);

        if (!$gamePlayerOut || !$gamePlayerIn) {
            return new JsonResponse(['error' => 'GamePlayer not found'], 404);
        }

        // Le joueur entrant reprend la position du sortant (nécessaire pour la rotation)
        $gamePlayerIn->setIsOnCourt(true);
        $gamePlayerIn->setPosition($gamePlayerOut->getPosition());
        $gamePlayerOut->setIsOnCourt(false);
        $gamePlayerOut->setPosition(null);
        $entityManager->flush();

        return new JsonResponse([
            'message'       => 'Substitution effectuée',
            'player_out_id' => $playerOut->getId(),
            'player_in_id'  => $playerIn->getId(),
        ]);
    }
    #[Route('/game/{gameId}/player/{playerId}/rename', name: 'app_game_player_rename', methods: ['POST'])]
    public function renamePlayer(int $gameId, int $playerId, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $name = trim((string) ($data['name'] ?? ''));

        if ($name === '' || mb_strlen($name) > 255) {
            return new JsonResponse(['error' => 'Nom invalide'], 400);
        }

        $game   = $entityManager->getRepository(Game::class)->find($gameId);
        $player = $entityManager->getRepository(Player::class)->find($playerId);

        if (!$game || !$player) {
            return new JsonResponse(['error' => 'Game or Player not found'], 404);
        }

        // Le joueur doit faire partie de ce match
        $gamePlayer = $entityManager->getRepository(GamePlayer::class)->findOneBy([
            'game'   => $game,
            'player' => $player,
        ]);

        if (!$gamePlayer) {
            return new JsonResponse(['error' => 'Player not in this game'], 404);
        }

        $player->setName($name);
        $entityManager->flush();

        return new JsonResponse([
            'message'   => 'Joueur renommé',
            'player_id' => $player->getId(),
            'name'      => $player->getName(),
        ]);
    }

    #[Route('/game/{gameId}/lineup', name: 'app_game_lineup', methods: ['GET'])]
    public function getLineup(int $gameId, EntityManagerInterface $entityManager): JsonResponse
    {
        $game = $entityManager->getRepository(Game::class)->find($gameId);

        if (!$game) {
            return new JsonResponse(['error' => 'Game not found'], 404);
        }

        // Joueurs de chaque équipe, indexés par id d'équipe
        $result = [];
        foreach ($game->getGamePlayers() as $gp) {
            $result[$gp->getTeam()->getId()][] = [
                'id'       => $gp->getPlayer()->getId(),
                'name'     => $gp->getPlayer()->getName(),
                'on_court' => (bool) $gp->isOnCourt(),
                'position' => $gp->getPosition(),
            ];
        }

        return new JsonResponse($result);
    }

    #[Route('/game/{gameId}/lineup', name: 'app_game_lineup_save', methods: ['POST'])]
    public function saveLineup(int $gameId, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data      = json_decode($request->getContent(), true);
        $teamId    = $data['team_id'] ?? null;
        $positions = $data['positions'] ?? [];

        $game = $entityManager->getRepository(Game::class)->find($gameId);
        $team = $entityManager->getRepository(Team::class)->find($teamId);

        if (!$game || !$team || $team->getGame() !== $game) {
            return new JsonResponse(['error' => 'Game or Team not found'], 404);
        }

        // 6 joueurs distincts sur les positions 1 à 6
        $byPlayer = [];
        foreach ($positions as $p) {
            $byPlayer[(int) ($p['player_id'] ?? 0)] = (int) ($p['position'] ?? 0);
        }
        $positionValues = array_values($byPlayer);
        sort($positionValues);
        if (count($positions) !== 6 || count($byPlayer) !== 6 || $positionValues !== [1, 2, 3, 4, 5, 6]) {
            return new JsonResponse(['error' => 'Il faut 6 joueurs différents sur les positions 1 à 6'], 400);
        }

        $gamePlayers = $entityManager->getRepository(GamePlayer::class)->findBy([
            'game' => $game,
            'team' => $team,
        ]);

        $teamPlayerIds = array_map(fn($gp) => $gp->getPlayer()->getId(), $gamePlayers);
        if (array_diff(array_keys($byPlayer), $teamPlayerIds)) {
            return new JsonResponse(['error' => 'Player not in this team'], 400);
        }

        foreach ($gamePlayers as $gp) {
            $position = $byPlayer[$gp->getPlayer()->getId()] ?? null;
            $gp->setIsOnCourt($position !== null);
            $gp->setPosition($position);
        }
        $entityManager->flush();

        return new JsonResponse(['message' => 'Composition enregistrée']);
    }

    #[Route('/game/{gameId}/positions', name: 'app_game_positions', methods: ['POST'])]
    public function updatePositions(int $gameId, Request $request, EntityManagerInterface $entityManager): JsonResponse
{
    $data    = json_decode($request->getContent(), true);
    $teamId  = $data['team_id'] ?? null;
    $updates = $data['positions'] ?? [];

    $game = $entityManager->getRepository(Game::class)->find($gameId);
    $team = $entityManager->getRepository(Team::class)->find($teamId);

    if (!$game || !$team) {
        return new JsonResponse(['error' => 'Not found'], 404);
    }

    foreach ($updates as $update) {
        $player = $entityManager->getRepository(Player::class)->find($update['player_id']);
        if (!$player) continue;

        $gp = $entityManager->getRepository(GamePlayer::class)->findOneBy([
            'game'   => $game,
            'team'   => $team,
            'player' => $player
        ]);

        if ($gp) {
            $gp->setPosition($update['position']);
        }
    }

    $entityManager->flush();

    return new JsonResponse(['message' => 'Positions updated']);
}
}