<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EvenementRepository::class)
 */
class Evenement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom de l'événement est requis.")
     * @Assert\Length(max=255, maxMessage="Le nom de l'événement ne peut pas dépasser {{ limit }} caractères.")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le lieu de l'événement est requis.")
     * @Assert\Length(max=255, maxMessage="Le lieu de l'événement ne peut pas dépasser {{ limit }} caractères.")
     */
    private $lieu;

    /**
     * @ORM\Column(type="string", length=1000)
     * @Assert\NotBlank(message="La description de l'événement est requise.")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="La capacité de l'événement est requise.")
     * @Assert\Positive(message="La capacité doit être supérieure à zéro.")
     */
    private $capacite;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank(message="La date de l'événement est requise.")
     * @Assert\GreaterThanOrEqual("today", message="La date de l'événement doit être postérieure ou égale à aujourd'hui.")
     */
    private $evenement_date;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="evenement_id")
     */
    private $evenement;

    public function __construct()
    {
        $this->evenement = new ArrayCollection();
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

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): self
    {
        $this->capacite = $capacite;

        return $this;
    }

    public function getEvenementDate(): ?\DateTimeInterface
    {
        return $this->evenement_date;
    }

    public function setEvenementDate(\DateTimeInterface $evenement_date): self
    {
        $this->evenement_date = $evenement_date;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getEvenement(): Collection
    {
        return $this->evenement;
    }

    public function addEvenement(Reservation $evenement): self
    {
        if (!$this->evenement->contains($evenement)) {
            $this->evenement[] = $evenement;
            $evenement->setEvenement($this);
        }

        return $this;
    }

    public function removeEvenement(Reservation $evenement): self
    {
        if ($this->evenement->removeElement($evenement)) {
            // set the owning side to null (unless already changed)
            if ($evenement->getEvenement() === $this) {
                $evenement->setEvenement(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return (string) $this->id;
    }
}
