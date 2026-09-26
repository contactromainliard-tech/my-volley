<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Player;
use App\Entity\GamePlayer;
use App\Entity\Manche;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/game/{id}', name: 'app_game_view', methods: ['GET'])]
    public function game(Game $game): Response
    {
        $teams = $game->getTeams();
        $team1 = $teams->first() ?? null;
        $team2 = $teams->last() ?? null;

        $currentManche = null;
foreach ($game->getManches() as $manche) {
    if ($manche->getStatus() === 'in_progress') {
        $currentManche = $manche;
        break;
    }
}

// 2. Passe la bonne variable au template
return $this->render('game/view.html.twig', [
    'game' => $game,
    'team1' => $team1,
    'team2' => $team2,
    'currentManche' => $currentManche, // ← corrigé
]);
    }

}
