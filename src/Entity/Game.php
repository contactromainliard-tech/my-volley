<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    /**
     * @var Collection<int, Team>
     */
    #[ORM\OneToMany(targetEntity: Team::class, mappedBy: 'game', orphanRemoval: true)]
    private Collection $teams;

    #[ORM\ManyToOne(inversedBy: 'games')]
    private ?Team $winnerTeam = null;

    /**
     * @var Collection<int, GamePlayer>
     */
    #[ORM\OneToMany(targetEntity: GamePlayer::class, mappedBy: 'game')]
    private Collection $gamePlayers;

    /**
     * @var Collection<int, Manche>
     */
    #[ORM\OneToMany(targetEntity: Manche::class, mappedBy: 'game', orphanRemoval: true)]
    private Collection $manches;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
        $this->gamePlayers = new ArrayCollection();
        $this->manches = new ArrayCollection();
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

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Team>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): static
    {
        if (!$this->teams->contains($team)) {
            $this->teams->add($team);
            $team->setGame($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): static
    {
        if ($this->teams->removeElement($team)) {
            // set the owning side to null (unless already changed)
            if ($team->getGame() === $this) {
                $team->setGame(null);
            }
        }

        return $this;
    }

    public function getWinnerTeam(): ?Team
    {
        return $this->winnerTeam;
    }

    public function setWinnerTeam(?Team $winnerTeam): static
    {
        $this->winnerTeam = $winnerTeam;

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
            $gamePlayer->setGame($this);
        }

        return $this;
    }

    public function removeGamePlayer(GamePlayer $gamePlayer): static
    {
        if ($this->gamePlayers->removeElement($gamePlayer)) {
            // set the owning side to null (unless already changed)
            if ($gamePlayer->getGame() === $this) {
                $gamePlayer->setGame(null);
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

    public function addManche(Manche $set): static
    {
        if (!$this->manches->contains($set)) {
            $this->manches->add($set);
            $set->setGame($this);
        }

        return $this;
    }

    public function removeManche(Manche $set): static
    {
        if ($this->manches->removeElement($set)) {
            // set the owning side to null (unless already changed)
            if ($set->getGame() === $this) {
                $set->setGame(null);
            }
        }

        return $this;
    }
}
