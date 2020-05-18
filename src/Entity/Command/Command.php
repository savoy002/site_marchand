<?php

namespace App\Entity\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use DateTime;

use App\Entity\Command\Adress;
use App\Entity\Command\Delivery;
use App\Entity\Command\PieceCommand;
use App\Entity\Product\VariantProduct;


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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateReceive;

    /**
     * @ORM\Column(type="integer")
     */
    private $priceTotal;

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
        $this->priceTotal = null;
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

    public function getDateReceive(): ?DateTime
    {
        return $this->dateReceive;
    }

    public function setDateReceive(DateTime $dateReceive)
    {
        $this->dateReceive = $dateReceive;

        return $this;
    }

    public function getPriceTotal(): ?int
    {
        return $this->priceTotal;
    }

    public function createPriceTotal(): self
    {
        if($priceTotal === null) {
            foreach ($this->getProducts as $pieceCommand)
                $this->priceTotal += $pieceCommand->getNbProducts * $pieceCommand->getProduct()->getPrice();
        }
        
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
