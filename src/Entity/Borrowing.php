<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BorrowingRepository")
 */
class Borrowing
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lendBy;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $borrowedBy;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Equipment")
     * @ORM\JoinColumn(nullable=false)
     */
    private $equipment;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startedOn;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endedOn;

    /**
     * @ORM\Column(type="integer")
     */
    private $allowedDays;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $remarks;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLendBy(): ?User
    {
        return $this->lendBy;
    }

    public function setLendBy(?User $lendBy): self
    {
        $this->lendBy = $lendBy;

        return $this;
    }

    public function getBorrowedBy(): ?User
    {
        return $this->borrowedBy;
    }

    public function setBorrowedBy(?User $borrowedBy): self
    {
        $this->borrowedBy = $borrowedBy;

        return $this;
    }

    public function getEquipment(): ?Equipment
    {
        return $this->equipment;
    }

    public function setEquipment(?Equipment $equipment): self
    {
        $this->equipment = $equipment;

        return $this;
    }

    public function getStartedOn(): ?\DateTimeInterface
    {
        return $this->startedOn;
    }

    public function setStartedOn(\DateTimeInterface $startedOn): self
    {
        $this->startedOn = $startedOn;

        return $this;
    }

    public function getEndedOn(): ?\DateTimeInterface
    {
        return $this->endedOn;
    }

    public function setEndedOn(\DateTimeInterface $endedOn): self
    {
        $this->endedOn = $endedOn;

        return $this;
    }

    public function getAllowedDays(): ?int
    {
        return $this->allowedDays;
    }

    public function setAllowedDays(int $allowedDays): self
    {
        $this->allowedDays = $allowedDays;

        return $this;
    }

    public function getRemarks(): ?string
    {
        return $this->remarks;
    }

    public function setRemarks(?string $remarks): self
    {
        $this->remarks = $remarks;

        return $this;
    }
}
