<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
           /**
     * @Groups({"post:read"})
     */
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
           /**
     * @Groups({"post:read"})
     */
    private ?string $Titre = null;

    #[ORM\Column(length: 255, nullable: true)]
           /**
     * @Groups({"post:read"})
     */
    private ?string $Description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
           /**
     * @Groups({"post:read"})
     */
    private ?\DateTimeInterface $Date = null;

    #[ORM\Column(length: 255, nullable: true)]
           /**
     * @Groups({"post:read"})
     */
    private ?string $State = null;

    #[ORM\ManyToOne(inversedBy: 'reclamations')]
           /**
     * @Groups({"post:read"})
     */
    private ?Produits $Produit = null;

    #[ORM\Column(length: 255, nullable: true)]
           /**
     * @Groups({"post:read"})
     */
    private ?string $Response = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->Titre;
    }

    public function setTitre(?string $Titre): self
    {
        $this->Titre = $Titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(?\DateTimeInterface $Date): self
    {
        $this->Date = $Date;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->State;
    }

    public function setState(?string $State): self
    {
        $this->State = $State;

        return $this;
    }

    public function getProduit(): ?Produits
    {
        return $this->Produit;
    }

    public function setProduit(?Produits $Produit): self
    {
        $this->Produit = $Produit;

        return $this;
    }

    public function getResponse(): ?string
    {
        return $this->Response;
    }

    public function setResponse(?string $Response): self
    {
        $this->Response = $Response;

        return $this;
    }
}
