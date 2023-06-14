<?php

namespace App\Entity;

use App\Repository\SocieteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SocieteRepository::class)
 */
class Societe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=NoteDeFrais::class, mappedBy="societe")
     */
    private $notesDeFrais;

    public function __construct()
    {
        $this->notesDeFrais = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
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

    /**
     * @return Collection<int, NoteDeFrais>
     */
    public function getNotesDeFrais(): Collection
    {
        return $this->notesDeFrais;
    }

    public function addNotesDeFrai(NoteDeFrais $notesDeFrai): self
    {
        if (!$this->notesDeFrais->contains($notesDeFrai)) {
            $this->notesDeFrais[] = $notesDeFrai;
            $notesDeFrai->setSociete($this);
        }

        return $this;
    }

    public function removeNotesDeFrai(NoteDeFrais $notesDeFrai): self
    {
        if ($this->notesDeFrais->removeElement($notesDeFrai)) {
            // set the owning side to null (unless already changed)
            if ($notesDeFrai->getSociete() === $this) {
                $notesDeFrai->setSociete(null);
            }
        }

        return $this;
    }
}
