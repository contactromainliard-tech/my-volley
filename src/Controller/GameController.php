<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Player;
use App\Entity\GamePlayer;
use App\Entity\Manche;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GameController extends AbstractController
{

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('game/index.html.twig', [
            'controller_name' => 'GameController',
        ]);
    }
    #[Route('/game/create', name: 'app_game_create', methods: ['POST'])]
    public function createGame(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Création du match
        $game = new Game();
        $game->setCreatedAt(new \DateTime());
        $game->setStatus('in_progress');
        $entityManager->persist($game);

        // Création des équipes
        $team1 = new Team();
        $team1->setName($request->request->get('team1_name'));
        $team1->setGame($game);
        $entityManager->persist($team1);

        $team2 = new Team();
        $team2->setName($request->request->get('team2_name'));
        $team2->setGame($game);
        $entityManager->persist($team2);    

        $entityManager->flush();

        // Création des joueurs pour chaque équipe

        $players1 = $request->request->all('team1_players');
        foreach ($players1 as $index => $playerName) {
            $player = new Player();
            $player->setName($playerName);
            $entityManager->persist($player);

            $gamePlayer = new GamePlayer();
            $gamePlayer->setGame($game);
            $gamePlayer->setTeam($team1);
            $gamePlayer->setPlayer($player);
            $gamePlayer->setIsOnCourt($index <6);
            $gamePlayer->setPosition($index < 6 ? $index + 1 : null);
            $entityManager->persist($gamePlayer);
        }

        $players2 = $request->request->all('team2_players');
        foreach ($players2 as $index => $playerName) {
            $player = new Player();
            $player->setName($playerName);
            $entityManager->persist($player);

            $gamePlayer = new GamePlayer();
            $gamePlayer->setGame($game);
            $gamePlayer->setTeam($team2);
            $gamePlayer->setPlayer($player);
            $gamePlayer->setIsOnCourt($index <6);
            $gamePlayer->setPosition($index < 6 ? $index + 1 : null);
            $entityManager->persist($gamePlayer);
        }

        // Création du premier set

        $manche = new Manche();
        $manche->setGame($game);
        $manche->setNumber(1);
        $manche->setScoreTeam1(0);
        $manche->setScoreTeam2(0);
        $manche->setStatus('in_progress');
        $entityManager->persist($manche);

        $entityManager->flush();

        return $this->redirectToRoute('app_game_view', ['id' => $game->getId()]);
    }
    #[Route('/game/service', name: 'app_game_service', methods: ['POST'])]
    public function setServiceTeam(Request $request, EntityManagerInterface $entityManager)
    {
        $data = json_decode($request->getContent(), true);
        $gameId = $data['game_id'] ?? null;
        $serviceTeamId = $data['service_team_id'] ?? null;

        if (!$gameId || !$serviceTeamId) {
            return new Response('Missing game_id or service_team_id', 400);
        }

        $game = $entityManager->getRepository(Game::class)->find($gameId);
        $serviceTeam = $entityManager->getRepository(Team::class)->find($serviceTeamId);

        if (!$game || !$serviceTeam) {
            return new Response('Game or Team not found', 404);
        }

        $game->setServiceTeam($serviceTeam);

        // Enregistre aussi sur la première manche
        $firstManche = $game->getManches()->first();
        if ($firstManche) {
            $firstManche->setStartingServiceTeam($serviceTeam);
            $entityManager->persist($firstManche);
        }

        $entityManager->persist($game);
        $entityManager->flush();

        return JsonResponse::fromJsonString(json_encode([
            'message' => 'Service team set successfully',
            'game_id' => $game->getId(),
            'service_team_id' => $serviceTeam->getId(),
        ]));
    }
    #[Route('/game/{gameId}/continue', name: 'app_game_continue', methods: ['POST'])]
    public function continueGame(int $gameId, EntityManagerInterface $entityManager): Response
    {
        $game = $entityManager->getRepository(Game::class)->find($gameId);

        if (!$game) {
            return new Response('Game not found', 404);
        }

        $game->setStatus('in_progress');
        $game->setWinnerTeam(null);
        $entityManager->persist($game);

        // Créer un nouveau set
        $manche = new Manche();
        $manche->setGame($game);
        $manche->setNumber($game->getManches()->count() + 1);
        $manche->setScoreTeam1(0);
        $manche->setScoreTeam2(0);
        $manche->setStatus('in_progress');
        $entityManager->persist($manche);

        $entityManager->flush();

        return JsonResponse::fromJsonString(json_encode([
            'message' => 'New set created successfully',
            'game_id' => $game->getId(),
            'manche_id' => $manche->getId(),
            'manche_number' => $manche->getNumber(),
            'service_team' => $game->getServiceTeam()?->getId(),
            'game_status' => $game->getStatus(),
            'winner_name' => $game->getWinnerTeam()?->getName(),
        ]));

    }
    #[Route('/game/{id}', name: 'app_game_view', methods: ['GET'])]
    public function game(Game $game): Response
    {
        $teams = $game->getTeams();
        $team1 = $teams->first() ?? null;
        $team2 = $teams->last() ?? null;
        $setsTeam1 = 0;
        $setsTeam2 = 0;
        
        foreach ($game->getManches() as $manche) {
            if ($manche->getWinnerTeam() === $team1) $setsTeam1++;
            if ($manche->getWinnerTeam() === $team2) $setsTeam2++;
        }

        $currentManche = null;
        foreach ($game->getManches() as $manche) {
            if ($manche->getStatus() === 'in_progress') {
                $currentManche = $manche;
                break;
            }
        }

       return $this->render('game/view.html.twig', [
    'game'          => $game,
    'team1'         => $team1,
    'team2'         => $team2,
    'currentManche' => $currentManche,
    'setsTeam1'     => $setsTeam1,
    'setsTeam2'     => $setsTeam2,
    'serviceTeam'   => $game->getServiceTeam(),
]);
    }
}
