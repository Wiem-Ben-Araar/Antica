<?php

namespace App\Entity;

use App\Repository\EnchereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EnchereRepository::class)
 */
class Enchere
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    public $prix_initiale;

    /**
     * @ORM\Column(type="integer")
     */
    public $prix_finale;

    /**
     * @ORM\Column(type="date")
     */
    private $date_creation;

    /**
     * @ORM\Column(type="date")
     */
    private $date_fermeture;

    /**
     * @ORM\OneToMany(targetEntity=Mise::class, mappedBy="enchere")
     */
    private $mises;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrixInitiale(): ?int
    {
        return $this->prix_initiale;
    }

    public function setPrixInitiale(int $prix_initiale): self
    {
        $this->prix_initiale = $prix_initiale;

        return $this;
    }

    public function getPrixFinale(): ?int
    {
        return $this->prix_finale;
    }

    public function setPrixFinale(int $prix_finale): self
    {
        $this->prix_finale = $prix_finale;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getDateFermeture(): ?\DateTimeInterface
    {
        return $this->date_fermeture;
    }

    public function setDateFermeture(\DateTimeInterface $date_fermeture): self
    {
        $this->date_fermeture = $date_fermeture;

        return $this;
    }

    public function addEnchere(Mise $mise): self
    {
        if (!$this->mises->contains($mise)) {
            $this->mises[] = $mise;
            $mise->setEnchere($this);
        }
        return $this;
    }

    public function removeEnchere(Mise $mise): self
    {
        if ($this->mises->removeElement($mise)) {
            // set the owning side to null (unless already changed)
            if ($mise->getEnchere() === $this) {
                $mise->setEnchere(null);
            }
        }
        return $this;
    }

    public function __toString()
    {
        return (string) $this->id;
    }
}
