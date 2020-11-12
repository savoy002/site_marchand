<?php

namespace App\Entity\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Entity\User\User;
use App\Entity\Command\Command;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Command\AddressRepository")
 * @ORM\Table(name="Address")
 */
class Address
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, name="street")
     */
    private $street;

    /**
     * @ORM\Column(type="string", name="zip_code")
     */
    private $zipCode;

    /**
     * @ORM\Column(type="string", length=255, name="city")
     */
    private $city;

    /**
     * @ORM\Column(type="boolean", name="deleted_adr", options={"default":false})
     */
    private $delete;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Command\Command", mappedBy="placeDel")
     */
    private $commands;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User\User", mappedBy="live")
     */
    private $belongs;

    public function __construct() 
    {
        $this->delete = false;
        $this->commands = new ArrayCollection();
        $this->belongs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

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

    public function getCommands(): Collection
    {
        return $this->commands;
    }

    public function isEmptyCommand(): bool
    {
        return $this->commands->isEmpty();
    }

    public function hasCommand(Command $command): bool
    {
        return $this->commands->contains($command);
    }

    public function addCommand(Command $command): self
    {
        if(!$this->commands->contains($command)) {
            $this->commands[] = $command;
            if($command->getPlaceDel() != $this) 
                $command->setPlaceDel($this);
        }

        return $this;
    }

    public function removeCommand(Command $command): self
    {
        if($this->commands->contains($command)) {
            $this->commands->removeElement($command);
            if($command->getPlaceDel() === $this)
                $command->setPlaceDel(null);
        }

        return $this;
    }

    public function getBelongs(): Collection
    {
        return $this->belongs;
    }

    public function isEmptyBelong(): bool
    {
        return $this->belongs->isEmpty();
    }

    public function hasBelong(?User $user): bool
    {
        return $this->belongs->contains($user);
    }

    public function addBelong(User $user): self
    {
        if(!$this->hasBelong($user)) {
            $this->belongs[] = $user;
            if($user->getLive() != $this)
                $user->setLive($this);
        }

        return $this;
    }

    public function removeBelong(User $user): self
    {
        if($this->hasBelong($user)) {
            $this->belongs->removeElement($user);
            if($user->getLive() === $this)
                $user->setLive(null);
        }

        return $this;
    }
    
}
