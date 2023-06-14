<?php

namespace App\Entity;

use App\Repository\NoteDeFraisRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NoteDeFraisRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class NoteDeFrais
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $dateDeLaNote;

    /**
     * @ORM\Column(type="float")
     */
    private $montant;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $dateDeCreation;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $dateDeModification;

    /**
     * @ORM\ManyToOne(targetEntity=TypeDeNote::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Societe::class, inversedBy="notesDeFrais")
     * @ORM\JoinColumn(nullable=false)
     */
    private $societe;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="notesDeFrais")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct() {
        $this->setDateDeCreation(new \DateTime());
        if ($this->getDateDeModification() == null) {
            $this->setDateDeModification(new \DateTime());
        }
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

    public function getDateDeLaNote(): ?\DateTimeInterface
    {
        return $this->dateDeLaNote;
    }

    public function setDateDeLaNote(\DateTimeInterface $dateDeLaNote): self
    {
        $this->dateDeLaNote = $dateDeLaNote;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDateDeCreation(): ?\DateTimeInterface
    {
        return $this->dateDeCreation;
    }

    public function setDateDeCreation(\DateTimeInterface $dateDeCreation): self
    {
        $this->dateDeCreation = $dateDeCreation;

        return $this;
    }

    public function getDateDeModification(): ?\DateTimeInterface
    {
        return $this->dateDeModification;
    }

    public function setDateDeModification(\DateTimeInterface $dateDeModification): self
    {
        $this->dateDeModification = $dateDeModification;

        return $this;
    }

    public function getType(): ?typeDeNote
    {
        return $this->type;
    }

    public function setType(?typeDeNote $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSociete(): ?societe
    {
        return $this->societe;
    }

    public function setSociete(?societe $societe): self
    {
        $this->societe = $societe;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updateModifiedDatetime() {
        $this->setDateDeModification(new \DateTime());
    }
}
