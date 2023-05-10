<?php

namespace App\Entity;

use App\Repository\ExpertiseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;


#[ORM\Entity(repositoryClass: ExpertiseRepository::class)]
class Expertise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_produit = null;

    #[ORM\Column]
    private ?int $id_artist = null;

    #[ORM\Column]
    #[Assert\Positive]
    private ?float $prix_estime = null;

    #[ORM\Column(length: 255)]
    private ?string $condition_produit = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdProduit(): ?int
    {
        return $this->id_produit;
    }

    public function setIdProduit(int $id_produit): self
    {
        $this->id_produit = $id_produit;

        return $this;
    }

    public function getIdArtist(): ?int
    {
        return $this->id_artist;
    }

    public function setIdArtist(int $id_artist): self
    {
        $this->id_artist = $id_artist;

        return $this;
    }

    public function getPrixEstime(): ?float
    {
        return $this->prix_estime;
    }

    public function setPrixEstime(float $prix_estime): self
    {
        $this->prix_estime = $prix_estime;

        return $this;
    }

    public function getConditionProduit(): ?string
    {
        return $this->condition_produit;
    }

    public function setConditionProduit(string $condition_produit): self
    {
        $this->condition_produit = $condition_produit;

        return $this;
    }
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('prix_estime', new Assert\Positive());
    }


}
