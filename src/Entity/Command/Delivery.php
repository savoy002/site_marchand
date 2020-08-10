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
     * @ORM\OneToMany(targetEntity="App\Entity\Command\Command", mappedBy="delivery")
     */
    private $commands;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Command\TypeDelivery", inversedBy="deliveries")
     * @ORM\JoinColumn(nullable=false, name="type_del_id_del", referencedColumnName="id")
     */
    private $type;

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

    public function getCommand(): Collection
    {
        return $this->commands;
    }

    public function hasCommand(?Command $command): bool
    {
        return $this->commands->contains($command);
    }

    public function addCommand(?Command $command): self
    {
        if(!$this->hasCommand($command)) {
            $this->commands[] = $command;

            if($command->getDelivery() != $this)
                $command->setDelivery($this);
        }
        return $this;
    }

    public function removeCommand(?Command $command): self
    {
        if($this->hasCommand($command)) {
            $this->commands->remove($command);

            if($command->getDelivery === $this)
                $command->setDelivery(null);
        }
        return $this;
    }


    public function getType(): ?TypeDelivery
    {
        return $this->type;
    }

    public function setType(?TypeDelivery $type): self
    {
        $this->type = $type;

        if(!$type->hasDelivery($this)) 
            $type->addDelivery($this);


        return $this;
    }

    
}
