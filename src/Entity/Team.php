<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'teams')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $game = null;

    /**
     * @var Collection<int, Game>
     */
    #[ORM\OneToMany(
        targetEntity: Game::class,
        mappedBy: 'winnerTeam'
    )]
    private Collection $games;

    /**
     * @var Collection<int, GamePlayer>
     */
    #[ORM\OneToMany(
        targetEntity: GamePlayer::class,
        mappedBy: 'team'
    )]
    private Collection $gamePlayers;

    /**
     * @var Collection<int, Manche>
     */
    #[ORM\OneToMany(
        targetEntity: Manche::class,
        mappedBy: 'winnerTeam'
    )]
    private Collection $manches;

    /**
     * @var Collection<int, Point>
     */
    #[ORM\OneToMany(
        targetEntity: Point::class,
        mappedBy: 'team',
        orphanRemoval: true
    )]
    private Collection $points;

    public function __construct()
    {
        $this->games = new ArrayCollection();
        $this->gamePlayers = new ArrayCollection();
        $this->manches = new ArrayCollection();
        $this->points = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): static
    {
        if (!$this->games->contains($game)) {
            $this->games->add($game);
            $game->setWinnerTeam($this);
        }

        return $this;
    }

    public function removeGame(Game $game): static
    {
        if ($this->games->removeElement($game)) {
            if ($game->getWinnerTeam() === $this) {
                $game->setWinnerTeam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GamePlayer>
     */
    public function getGamePlayers(): Collection
    {
        return $this->gamePlayers;
    }

    public function addGamePlayer(GamePlayer $gamePlayer): static
    {
        if (!$this->gamePlayers->contains($gamePlayer)) {
            $this->gamePlayers->add($gamePlayer);
            $gamePlayer->setTeam($this);
        }

        return $this;
    }

    public function removeGamePlayer(GamePlayer $gamePlayer): static
    {
        if ($this->gamePlayers->removeElement($gamePlayer)) {
            if ($gamePlayer->getTeam() === $this) {
                $gamePlayer->setTeam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Manche>
     */
    public function getManches(): Collection
    {
        return $this->manches;
    }

    public function addManche(Manche $manche): static
    {
        if (!$this->manches->contains($manche)) {
            $this->manches->add($manche);
            $manche->setWinnerTeam($this);
        }

        return $this;
    }

    public function removeManche(Manche $manche): static
    {
        if ($this->manches->removeElement($manche)) {
            if ($manche->getWinnerTeam() === $this) {
                $manche->setWinnerTeam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Point>
     */
    public function getPoints(): Collection
    {
        return $this->points;
    }

    public function addPoint(Point $point): static
    {
        if (!$this->points->contains($point)) {
            $this->points->add($point);
            $point->setTeam($this);
        }

        return $this;
    }

    public function removePoint(Point $point): static
    {
        if ($this->points->removeElement($point)) {
            if ($point->getTeam() === $this) {
                $point->setTeam(null);
            }
        }

        return $this;
    }
}