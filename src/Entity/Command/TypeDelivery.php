<?php

namespace App\Entity\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Command\TypeDeliveryRepository")
 * @ORM\Table(name="TypeDelivery")
 */
class TypeDelivery
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, name="name_type_del")
     */
    private $name;

    /**
     * @ORM\Column(type="integer", name="price_type_del")
     */
    private $price;

    /**
     * @ORM\Column(type="integer", name="time_min_type_del")
     */
    private $timeMin;

    /**
     * @ORM\Column(type="integer", name="time_max_type_del")
     */
    private $timeMax;

    /**
     * @ORM\Column(type="boolean", name="activate_type_del", options={"default":false})
     */
    private $activate;

    /**
     * @ORM\Column(type="boolean", name="deleted_type_del", options={"default":false})
     */
    private $delete;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Command\Delivery", mappedBy="type")
     */
    private $deliveries;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Command\CompanyDelivery", inversedBy="types")
     * @ORM\JoinColumn(nullable=false, name="comp_del_id_del", referencedColumnName="id")
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Command\TypeDeliverySelected", mappedBy="typeDelivery", orphanRemoval=true)
     */
    private $commands;

    ///**
    // * @ORM\OneToMany(targetEntity="App\Entity\Command\Command", mappedBy="typeDelSelected")
    // */
    //private $commands;

    public function __construct()
    {
        $this->activate = false;
        $this->delete = false;
        $this->deliveries = new ArrayCollection();
        $this->commands = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getTimeMin(): ?int
    {
        return $this->timeMin;
    }

    public function setTimeMin(int $timeMin): self
    {
        $this->timeMin = $timeMin;

        return $this;
    }

    public function getTimeMax(): ?int
    {
        return $this->timeMax;
    }

    public function setTimeMax(int $timeMax): self
    {
        $this->timeMax = $timeMax;

        return $this;
    }

    public function getActivate(): bool
    {
        return $this->activate;
    }

    public function setActivate(bool $activate): self
    {
        $this->activate = $activate;

        return $this;
    }

    public function getDelete(): bool 
    {
        return $this->delete;
    }

    public function setDelete(bool $delete): self 
    {
        $this->delete = $delete;
        return $this;
    }

    public function getDeliveries(): Collection
    {
        return $this->deliveries;
    }

    public function hasDelivery(Delivery $delivery): bool
    {
        return $this->deliveries->contains($delivery);
    }

    public function addDelivery(Delivery $delivery): self
    {
        if(!$this->hasDelivery($delivery)) {
            $this->delivery[] = $delivery;

            if($delivery->getType() != $this)
                $delivery->setType($this);
        }

        return $this;
    }

    public function removeDelivery(Delivery $delivery): self
    {
        if($this->hasDelivery($delivery)) {
            $this->delivery->removeElement($delivery);

            if($delivery->getType() === $this)
                $delivery->setType(null);
        }

        return $this;
    }

    public function getCompany(): ?CompanyDelivery
    {
        return $this->company;
    }

    public function setCompany(?CompanyDelivery $company): self
    {

        if($this->company !== null){
            if($company->hasType($this))
                $company->removeType($this);
        }

        $this->company = $company;

        if($company !== null){
            if(!$company->hasType($this))
                $company->addType($this);
        }

        return $this;
    }

    public function getCommands(): Collection
    {
        return $this->commands;
    }

    public function hasCommands(TypeDeliverySelected $command): bool
    {
        return $this->commands->contains($command);
    }

    public function addCommand(TypeDeliverySelected $command): self
    {
        if(!$this->hasCommands($command)) {
            $this->commands[] = $command;

            if($command->getTypeDelSelected() != $this)
                $command->setTypeDelSelected($this);
        }

        return $this;
    }

    public function removeCommand(TypeDeliverySelected $command): self
    {
        if($this->hasCommands($command)) {
            $this->commands->removeElement($command);

            if($command->getTypeDelSelected() === $this)
                $command->setTypeDelSelected(null);
        }

        return $this;
    }

    public function showTypeDelivery(): string
    {
        $price_100 = strval($this->getPrice());
        $price = substr($price_100, 0, strlen($price_100) - 2) . ',' . substr($price_100, strlen($price_100) - 2, strlen($price_100));

        return $this->getName().' '.$price;
    }

    ///**
    // * @return Collection|TypeDeliverySelected[]
    // */
    /*public function getCommand(): Collection
    {
        return $this->command;
    }

    public function addCommand(TypeDeliverySelected $command): self
    {
        if (!$this->command->contains($command)) {
            $this->command[] = $command;
            $command->setTypeDelivery($this);
        }

        return $this;
    }

    public function removeCommand(TypeDeliverySelected $command): self
    {
        if ($this->command->contains($command)) {
            $this->command->removeElement($command);
            // set the owning side to null (unless already changed)
            if ($command->getTypeDelivery() === $this) {
                $command->setTypeDelivery(null);
            }
        }

        return $this;
    }*/

}
