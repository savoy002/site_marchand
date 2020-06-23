<?php

namespace App\Entity\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Entity\User\User;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Command\AdressRepository")
 * @ORM\Table(name="Adress")
 */
class Adress
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
     * @ORM\Column(type="integer", name="zip_code")
     */
    private $zipCode;

    /**
     * @ORM\Column(type="string", length=255, name="city")
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Command\Command", mappedBy="place_del")
     */
    private $commands;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User\User", mappedBy="live")
     */
    private $belongs;

    public function __construct() 
    {
        $this->belongs = ArrayCollection();
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

    public function getZipCode(): ?int
    {
        return $this->zipCode;
    }

    public function setZipCode(int $zipCode): self
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

    public function getCommands(): Collections
    {
        return $this->commands;
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
                $command->setPlaceDel($this);
        }

        return $this;
    }

    public function setCommands(?Command $commands): self
    {
        $this->commands = $commands;

        return $this;
    }

    public function getbelongs(): ArrayCollection
    {
        return $this->belongs;
    }

    public function addBelongs(User $user): self
    {
        if(!$this->belongs->contains($user)) {
            $this->belongs[] = $user;
            $user->setLive($this);
        }

        return $this;
    }

    public function removeBelongs(User $user): self
    {
        if($this->belongs->contains($user)) {
            $this->belongs->removeElement($user);
            if($user->getLive() === $this)
                $user->setLive(null);
        }

        return $this;
    }
    
}
