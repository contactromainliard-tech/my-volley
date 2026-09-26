<?php

namespace App\Entity;

use App\Repository\PointRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PointRepository::class)]
class Point
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'points')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Manche $manche = null;

    #[ORM\ManyToOne(inversedBy: 'points')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Team $team = null;

    #[ORM\ManyToOne(inversedBy: 'points')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Player $player = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $sequence_number = null;

    #[ORM\Column]
    private ?bool $is_cancelled = false;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $cancelled_at = null;

    #[ORM\Column]
    private ?\DateTime $created_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getManche(): ?Manche
    {
        return $this->manche;
    }

    public function setManche(?Manche $manche): static
    {
        $this->manche = $manche;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): static
    {
        $this->team = $team;

        return $this;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): static
    {
        $this->player = $player;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getSequenceNumber(): ?int
    {
        return $this->sequence_number;
    }

    public function setSequenceNumber(int $sequence_number): static
    {
        $this->sequence_number = $sequence_number;

        return $this;
    }

    public function isCancelled(): ?bool
    {
        return $this->is_cancelled;
    }

    public function setIsCancelled(bool $is_cancelled): static
    {
        $this->is_cancelled = $is_cancelled;

        return $this;
    }

    public function getCancelledAt(): ?\DateTime
    {
        return $this->cancelled_at;
    }

    public function setCancelledAt(\DateTime $cancelled_at): static
    {
        $this->cancelled_at = $cancelled_at;

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
}
