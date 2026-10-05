<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Player;
use App\Entity\Point;
use App\Entity\GamePlayer;
use App\Entity\Manche;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;
use PHPUnit\Util\Json;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class PdfController extends AbstractController
{
    #[Route('/game/{gameId}/pdf', name: 'app_game_pdf', methods: ['GET'])]
    public function generatePdf(int $gameId, EntityManagerInterface $entityManager): Response
    {

        $game = $entityManager->getRepository(Game::class)->find($gameId);
        $gameScores = $entityManager->getRepository(Manche::class)->findBy(['game' => $gameId]);
        $gamePlayers = $entityManager->getRepository(GamePlayer::class)->findBy(['game' => $gameId]);
        $gameTeam = $entityManager->getRepository(Team::class)->findBy(['game' => $gameId]);
        
        $points = [];
        foreach ($gameScores as $manche) {
            $manchePoints = $entityManager->getRepository(Point::class)->findBy([
            'manche'       => $manche,
            'is_cancelled' => false
        ]);
        $points = array_merge($points, $manchePoints);
        }

        // Organiser les points par manche
        $pointsByManche = [];
        foreach ($gameScores as $manche) {
            $manchePoints = $entityManager->getRepository(Point::class)->findBy([
                'manche'       => $manche,
                'is_cancelled' => false,
            ], ['sequence_number' => 'ASC']);
            $pointsByManche[$manche->getId()] = $manchePoints;
        }
        // Rendu du template HTML
        $html = $this->renderView('pdf/match.html.twig', [
            'game'            => $game,
            'teams'           => $gameTeam,
            'manches'         => $gameScores,
            'players'         => $gamePlayers,
            'points'          => $points,
            'pointsByManche'  => $pointsByManche,
        ]);

        // Génération du PDF avec DomPDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="feuille-match-'.$gameId.'.pdf"',
            ]
        );
    }
}
