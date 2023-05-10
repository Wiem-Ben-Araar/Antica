<?php

namespace App\Entity;

use App\Repository\ProduitsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Serializer\Annotation\Groups;
#[ORM\Entity(repositoryClass: ProduitsRepository::class)]
#[Vich\Uploadable]
class Produits
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column]
        /**
     * @Groups({"post:read"})
     */
    private ?int $id = null;

    #[ORM\Column(length: 255)]
        /**
     * @Groups({"post:read"})
     */
    private ?string $nom = null;

    #[ORM\Column]
        /**
     * @Groups({"post:read"})
     */
    private ?float $prix = null;
    #[ORM\OneToOne(targetEntity: Panier::class, mappedBy: "produit")]
    
    private ?Panier $panier;

    #[ORM\Column(length: 255, nullable: true)]
        /**
     * @Groups({"post:read"})
     */
    private ?string $image = null;

    #[Vich\UploadableField(mapping: 'image', fileNameProperty: 'image')]
    #[Assert\File(
        maxSize: "8M",
        mimeTypes: "image/*",
        maxSizeMessage: "max 8M"
    )]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255)]
        /**
     * @Groups({"post:read"})
     */
    private ?string $genre = null;

    #[ORM\OneToMany(mappedBy: 'Produit', targetEntity: Reclamation::class)]
    
    private Collection $reclamations;

    public function __construct()
    {
        $this->reclamations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }
    public function __toString() {
        return $this->nom;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): self
    {
        $this->imageFile = $imageFile;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): self
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations->add($reclamation);
            $reclamation->setProduit($this);
        }

        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): self
    {
        if ($this->reclamations->removeElement($reclamation)) {
            // set the owning side to null (unless already changed)
            if ($reclamation->getProduit() === $this) {
                $reclamation->setProduit(null);
            }
        }

        return $this;
    }
}
