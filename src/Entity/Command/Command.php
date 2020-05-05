<?php

namespace App\Entity\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Command\CommandRepository")
 * @ORM\Table(name="Command")
 */
class Command
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", name="create_at_com")
     */
    private $createAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $completed;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Command\Adress", inversedBy="commands")
     * @ORM\JoinColumn(name="adr_id_com", referencedColumnName="id")
     */
    private $placeDel;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Command\Delivery", inversedBy="commands")
     * @ORM\JoinColumn(name="del_id_com", referencedColumnName="id")
     */
    private $typeDelivery;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Command\PieceCommand", mappedBy="command", orphanRemoval=true)
     */
    private $products;

    public function __construct()
    {
        $this->placeDel = new ArrayCollection();
        $this->delivery = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCom(): ?\DateTimeInterface
    {
        return $this->dateCom;
    }

    public function setDateCom(\DateTimeInterface $dateCom): self
    {
        $this->dateCom = $dateCom;

        return $this;
    }

    public function getCompleted(): ?bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): self
    {
        $this->completed = $completed;

        return $this;
    }

    public function getIsDel(): ?bool
    {
        return $this->isDel;
    }

    public function setIsDel(bool $isDel): self
    {
        $this->isDel = $isDel;

        return $this;
    }

    /**
     * @return Collection|Adress[]
     */
    public function getPlaceDel(): Collection
    {
        return $this->placeDel;
    }

    public function addPlaceDel(Adress $placeDel): self
    {
        if (!$this->placeDel->contains($placeDel)) {
            $this->placeDel[] = $placeDel;
            $placeDel->setCommands($this);
        }

        return $this;
    }

    public function removePlaceDel(Adress $placeDel): self
    {
        if ($this->placeDel->contains($placeDel)) {
            $this->placeDel->removeElement($placeDel);
            // set the owning side to null (unless already changed)
            if ($placeDel->getCommands() === $this) {
                $placeDel->setCommands(null);
            }
        }

        return $this;
    }

    public function getTypeDelivery(): ?Delivery
    {
        return $this->typeDelivery;
    }

    public function setTypeDelivery(?Delivery $typeDelivery): self
    {
        $this->typeDelivery = $typeDelivery;

        return $this;
    }

    /**
     * @return Collection|PieceCommand[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(PieceCommand $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setCommand($this);
        }

        return $this;
    }

    public function removeProduct(PieceCommand $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getCommand() === $this) {
                $product->setCommand(null);
            }
        }

        return $this;
    }

}
