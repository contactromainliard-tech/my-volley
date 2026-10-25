<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Player;
use App\Entity\Point;
use App\Entity\GamePlayer;
use App\Entity\Manche;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class PdfController extends AbstractController
{
    #[Route('/game/{gameId}/pdf', name: 'app_game_pdf', methods: ['GET'])]
    public function generatePdf(int $gameId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $spoil = $request->query->getBoolean('spoil', true);

        $game = $entityManager->getRepository(Game::class)->find($gameId);
        $gameScores = $entityManager->getRepository(Manche::class)->findBy(['game' => $gameId]);
        $gamePlayers = $entityManager->getRepository(GamePlayer::class)->findBy(['game' => $gameId]);
        $gameTeam = $entityManager->getRepository(Team::class)->findBy(['game' => $gameId]);

        $points = [];
        foreach ($gameScores as $manche) {
            $manchePoints = $entityManager->getRepository(Point::class)->findBy([
                'manche'       => $manche,
                'is_cancelled' => false,
            ]);
            $points = array_merge($points, $manchePoints);
        }

        $pointsByManche = [];
        foreach ($gameScores as $manche) {
            $manchePoints = $entityManager->getRepository(Point::class)->findBy([
                'manche'       => $manche,
                'is_cancelled' => false,
            ], ['sequence_number' => 'ASC']);
            $pointsByManche[$manche->getId()] = $manchePoints;
        }

        // IDs des joueurs par équipe
        $team1PlayerIds = [];
        $team2PlayerIds = [];
        foreach ($gamePlayers as $gp) {
            if ($gp->getTeam()->getId() === $gameTeam[0]->getId()) {
                $team1PlayerIds[] = $gp->getPlayer()->getId();
            } else {
                $team2PlayerIds[] = $gp->getPlayer()->getId();
            }
        }

        $template = $spoil ? 'pdf/match.html.twig' : 'pdf/match_no_spoil.html.twig';

        $html = $this->renderView($template, [
            'game'           => $game,
            'teams'          => $gameTeam,
            'manches'        => $gameScores,
            'players'        => $gamePlayers,
            'points'         => $points,
            'pointsByManche' => $pointsByManche,
            'team1PlayerIds' => $team1PlayerIds,
            'team2PlayerIds' => $team2PlayerIds,
        ]);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = $spoil ? 'feuille-match-' : 'feuille-match-sans-fautes-';

        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$filename.$gameId.'.pdf"',
            ]
        );
    }
}