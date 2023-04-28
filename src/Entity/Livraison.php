<?php

namespace App\Entity;

use App\Repository\LivraisonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LivraisonRepository::class)]
class Livraison
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_livraison = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statut = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'livraisons', cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getDateLivraison(): ?\DateTimeInterface
    {
        return $this->date_livraison;
    }

    public function setDateLivraison(?\DateTimeInterface $date_livraison): self
    {
        // Vérification si la date de livraison est dans le futur
        if ($date_livraison < new \DateTime()) {
            throw new \InvalidArgumentException("La date de livraison ne peut pas être dans le passé");
        }

        // Vérification si la date de livraison est le jour même
        if ($date_livraison->format('Y-m-d') === (new \DateTime())->format('Y-m-d')) {
            throw new \InvalidArgumentException("La date de livraison ne peut pas être le jour même");
        }

        // Vérification si la date de livraison est moins d'un jour après la date actuelle
        $interval = (new \DateTime())->diff($date_livraison);
        if ($interval->days < 1) {
            throw new \InvalidArgumentException("La date de livraison doit être au moins un jour après la date actuelle");
        }

        $this->date_livraison = $date_livraison;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
    public function getProduitPrix(): float
    {
        return $this->produit->getPrix();
    }


}
