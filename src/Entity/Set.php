<?php

namespace App\Entity;

use App\Repository\SetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SetRepository::class)]
class Set
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $game = null;

    #[ORM\Column]
    private ?int $number = null;

    #[ORM\Column]
    private ?int $score_team1 = null;

    #[ORM\Column]
    private ?int $score_team2 = null;

    #[ORM\ManyToOne(inversedBy: 'sets')]
    private ?Team $winner_team_id = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    /**
     * @var Collection<int, Point>
     */
    #[ORM\OneToMany(targetEntity: Point::class, mappedBy: 'set', orphanRemoval: true)]
    private Collection $points;

    public function __construct()
    {
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

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getScoreTeam1(): ?int
    {
        return $this->score_team1;
    }

    public function setScoreTeam1(int $score_team1): static
    {
        $this->score_team1 = $score_team1;

        return $this;
    }

    public function getScoreTeam2(): ?int
    {
        return $this->score_team2;
    }

    public function setScoreTeam2(int $score_team2): static
    {
        $this->score_team2 = $score_team2;

        return $this;
    }

    public function getWinnerTeamId(): ?Team
    {
        return $this->winner_team_id;
    }

    public function setWinnerTeamId(?Team $winner_team_id): static
    {
        $this->winner_team_id = $winner_team_id;

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
            $point->setSet($this);
        }

        return $this;
    }

    public function removePoint(Point $point): static
    {
        if ($this->points->removeElement($point)) {
            // set the owning side to null (unless already changed)
            if ($point->getSet() === $this) {
                $point->setSet(null);
            }
        }

        return $this;
    }
}
