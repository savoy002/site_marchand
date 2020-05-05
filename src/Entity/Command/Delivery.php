<?php

namespace App\Entity\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Command\DeliveryRepository")
 * @ORM\Table(name="Delivery")
 */
class Delivery
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date", name="date_del", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="integer", name="price_del")
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Command\Command", mappedBy="typeDelivery")
     */
    private $commands;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Command\CompanyDelivery", inversedBy="deliveries")
     * @ORM\JoinColumn(nullable=false, name="comp_del_id_del", referencedColumnName="id")
     */
    private $companyDelivery;

    public function __construct()
    {
        $this->commands = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection|Command[]
     */
    public function getCommands(): Collection
    {
        return $this->commands;
    }

    public function addCommand(Command $command): self
    {
        if (!$this->commands->contains($command)) {
            $this->commands[] = $command;
            $command->setTypeDelivery($this);
        }

        return $this;
    }

    public function removeCommand(Command $command): self
    {
        if ($this->commands->contains($command)) {
            $this->commands->removeElement($command);
            // set the owning side to null (unless already changed)
            if ($command->getTypeDelivery() === $this) {
                $command->setTypeDelivery(null);
            }
        }

        return $this;
    }

    public function getCompanyDelivery(): ?CompanyDelivery
    {
        return $this->companyDelivery;
    }

    public function setCompanyDelivery(?CompanyDelivery $companyDelivery): self
    {
        $this->companyDelivery = $companyDelivery;

        return $this;
    }

    
}
