<?php

namespace App\Entity;

use DateTimeInterface;

class DisponibiliteSearch{
    private ?\DateTimeInterface $dateDebut = null;
    private ?\DateTimeInterface $dateFin = null;
    private ?int $prixMax = null;

    

    /**
     * Get the value of dateDebut
     */
    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    /**
     * Set the value of dateDebut
     */
    public function setDateDebut(?\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get the value of prixMax
     */
    public function getPrixMax(): ?int
    {
        return $this->prixMax;
    }

    /**
     * Set the value of prixMax
     */
    public function setPrixMax(?int $prixMax): self
    {
        $this->prixMax = $prixMax;

        return $this;
    }

    /**
     * Get the value of dateFin
     */
    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    /**
     * Set the value of dateFin
     */
    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }
}
