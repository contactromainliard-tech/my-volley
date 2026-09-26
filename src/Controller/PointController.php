<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Player;
use App\Entity\GamePlayer;
use App\Entity\Manche;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PointController extends AbstractController
{
    #[Route('/game/{gameId}/add-point', name: 'app_add_point', methods: ['POST'])]
    public function addPoint(int $gameId, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupération du match, de la manche et de l'équipe du type de point et du joueur à partir de la requête
        
        $data = json_decode($request->getContent(), true);
        $playerId = $data['player_id'] ?? null;
        $type = $data['type'] ?? null;
        $mancheId = $data['manche_id'] ?? null;
        $teamId = $data['team_id'] ?? null;
        $game = $entityManager->getRepository(Game::class)->find($gameId);
        $manche = $entityManager->getRepository(Manche::class)->find($mancheId);
        $team = $entityManager->getRepository(Team::class)->find($teamId);
        $player = $playerId ? $entityManager->getRepository(Player::class)->find($playerId) : null;



        if (!$game || !$manche || !$team || !$type) {
            throw $this->createNotFoundException('Game, Manche, Team or Player not found');
        }

        // Création du point
        $point = new \App\Entity\Point();
        $point->setManche($manche);
        $point->setTeam($team);
        $point->setPlayer($player);
        $point->setCreatedAt(new \DateTime());
        $point->setType($type);
        $point->setIsCancelled(false);
        $activePoints = $manche->getPoints()->filter(fn($p) => $p->isCancelled() === false);
        $point->setSequenceNumber(count($activePoints) + 1);

        // Persistance du point
        $entityManager->persist($point);
        $entityManager->flush();

        $entityManager->refresh($manche);

        // Calcul des scores et sets

        $teams = $game->getTeams();
        $team1 = $teams->first();
        $team2 = $teams->last();

        $scoreTeam1 = $manche->getPoints()->filter(fn($p) => $p->getTeam() === $team1 && $p->isCancelled() === false)->count();
        $scoreTeam2 = $manche->getPoints()->filter(fn($p) => $p->getTeam() === $team2 && $p->isCancelled() === false)->count();

        $setsTeam1 = $game->getManches()->filter(fn($m) => $m->getWinnerTeam() === $team1)->count();

        $setsTeam2 = $game->getManches()->filter(fn($m) => $m->getWinnerTeam() === $team2)->count();

        if ($manche->getNumber() === 5) {
            if ($scoreTeam1 >= 15 && $scoreTeam1 - $scoreTeam2 >= 2) {
                $manche->setWinnerTeam($team1);
                $manche->setStatus('finished');
            } elseif ($scoreTeam2 >= 15 && $scoreTeam2 - $scoreTeam1 >= 2) {
                $manche->setWinnerTeam($team2);
                $manche->setStatus('finished');
            }
        } else {
            if ($scoreTeam1 >= 25 && $scoreTeam1 - $scoreTeam2 >= 2) {
                $manche->setWinnerTeam($team1);
                $manche->setStatus('finished');
            } elseif ($scoreTeam2 >= 25 && $scoreTeam2 - $scoreTeam1 >= 2) {
                $manche->setWinnerTeam($team2);
                $manche->setStatus('finished');
            }
        }

        $nextManche = null;

        if ($manche->getStatus() === 'finished') {
            // Vérification si le match est terminé
            $setsTeam1 = $game->getManches()->filter(fn($m) => $m->getWinnerTeam() === $team1)->count();
            $setsTeam2 = $game->getManches()->filter(fn($m) => $m->getWinnerTeam() === $team2)->count();

            if ($setsTeam1 === 3 || $setsTeam2 === 3) {
                $game->setStatus('finished');
                $game->setWinnerTeam($setsTeam1 === 3 ? $team1 : $team2);
            } else {
                // Création de la manche suivante
                $nextManche = new Manche();
                $nextManche->setGame($game);
                $nextManche->setNumber($manche->getNumber() + 1);
                $nextManche->setScoreTeam1(0);
                $nextManche->setScoreTeam2(0);
                $nextManche->setStatus('in_progress');
                $entityManager->persist($nextManche);
                $entityManager->flush();
            }
        }


        return JsonResponse::fromJsonString(json_encode([
            'message' => 'Point added successfully',
            'point_id' => $point->getId(),
            'score_team1' => isset($nextManche) ? 0 : $scoreTeam1,
            'score_team2' => isset($nextManche) ? 0 : $scoreTeam2,
            'sets_team1' => $setsTeam1,
            'sets_team2' => $setsTeam2,
            'manche_number' => isset($nextManche) ? $nextManche->getNumber() : $manche->getNumber(),
            'service_team' => null, // on gérera ça après
            'manche_id' => isset($nextManche) ? $nextManche->getId() : $manche->getId(),
        ]));
    }
}