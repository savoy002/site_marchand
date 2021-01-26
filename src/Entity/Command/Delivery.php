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
     * @ORM\Column(type="string", name="num_del", length=255, unique=true)
     */
    private $num;

    /**
     * @ORM\Column(type="date", name="date_del", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $departments = [];

    ///**
    // * @ORM\Column(type="boolean", name="empty_del")
    // */
    //private $empty;

    /**
     * @ORM\Column(type="boolean", name="deleted_del", options={"default":false})
     */
    private $delete;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Command\Command", mappedBy="delivery")
     */
    private $commands;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Command\CompanyDelivery", inversedBy="deliveries")
     * @ORM\JoinColumn(nullable=false, name="comp_del_id_del", referencedColumnName="id")
     */
    private $company;

    ///**
    // * @ORM\ManyToOne(targetEntity="App\Entity\Command\TypeDelivery", inversedBy="deliveries")
    // * @ORM\JoinColumn(nullable=false, name="type_del_id_del", referencedColumnName="id")
    // */
    //private $type;


    public function __construct()
    {
        $this->delete = false;
        $this->num = uniqid();
        $this->empty = true;
        $this->commands = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNum(): ?string
    {
        return $this->num;
    }

    public function setNum(string $num): self
    {
        $this->num = $num;

        return $this;
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

    public function getDepartments(): ?array
    {
        return $this->departments;
    }

    public function setDepartments(array $departments): self
    {
        $this->departments = $departments;

        return $this;
    }

    /*public function getEmpty(): ?bool
    {
        return $this->empty;
    }

    public function setEmpty(bool $empty): self
    {
        $this->empty = $empty;

        return $this;
    }*/

    public function getDelete(): bool 
    {
        return $this->delete;
    }

    public function setDelete(bool $delete): self 
    {
        $this->delete = $delete;
        return $this;
    }

    public function getCommands(): Collection
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

    public function getCompany(): ?CompanyDelivery
    {
        return $this->company;
    }

    public function setCompany(?CompanyDelivery $company): self
    {
        $this->company = $company;

        if(!$company->hasDelivery($this)) 
            $company->addDelivery($this);

        return $this;
    }


    /*public function getType(): ?TypeDelivery
    {
        return $this->type;
    }

    public function setType(?TypeDelivery $type): self
    {
        $this->type = $type;

        if(!$type->hasDelivery($this)) 
            $type->addDelivery($this);


        return $this;
    }*/

}
