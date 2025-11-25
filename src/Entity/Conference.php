<?php

namespace App\Entity;

use App\Repository\ConferenceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConferenceRepository::class)]
class Conference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    #[ORM\ManyToOne(inversedBy: 'conferences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pathologie $pathologie = null;

    #[ORM\ManyToOne(inversedBy: 'conferences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $medecin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getPathologie(): ?Pathologie
    {
        return $this->pathologie;
    }

    public function setPathologie(?Pathologie $pathologie): static
    {
        $this->pathologie = $pathologie;

        return $this;
    }

    public function getMedecin(): ?User
    {
        return $this->medecin;
    }

    public function setMedecin(?User $medecin): static
    {
        $this->medecin = $medecin;

        return $this;
    }
}
