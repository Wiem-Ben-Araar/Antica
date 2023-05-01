<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantite = 1;

    #[ORM\Column]
    private ?float $total = null;



    #[ORM\OneToOne(cascade: ['persist', 'remove'],targetEntity: Produits::class, inversedBy: "panier")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produits $produit = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'paniers', cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;






    public function __construct()
    {
        $this->livraisons = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        if ($quantite !== 1) {
            throw new \InvalidArgumentException('Il y a un seul produit!.');
        }
        $this->quantite = $quantite;

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



    public function getProduit(): ?Produits
    {
        return $this->produit;
    }

    public function setProduit(Produits $produit): self
    {
        $this->produit = $produit;

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
