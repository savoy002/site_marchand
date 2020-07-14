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
use App\Entity\User\User;

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

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User", inversedBy="commands")
     * @ORM\JoinColumn(name="user_id_com", referencedColumnName="id")
     */
    private $user;

    public function __construct()
    {
        $this->createAt = new DateTime();
        $this->completed = false;
        $this->priceTotal = null;
        $this->delivery = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreatedAt(\DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

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

    public function calculPriceTotal(): self
    {
        $this->priceTotal = 0;

        if(!empty($this->products)) {

            foreach ($this->products as $pieceCommand)
                $this->priceTotal += $pieceCommand->getNbProducts() * $pieceCommand->getProduct()->getPrice();

        }

        if($this->typeDelivery !== null)
            $this->priceTotal += $this->typeDelivery->getPrice();

        return $this;
    }

    public function getPlaceDel(): ?Adress
    {
        return $this->placeDel;
    }

    public function setPlaceDel(?Adress $adress): self
    {
        if($this->placeDel != null)
            $this->placeDel->removeCommand($this);
        $this->placeDel = $adress;
        if(!$adress->hasCommand($this))
            $adress->addCommand($this);

        return $this;
    }

    public function getTypeDelivery(): ?Delivery
    {
        return $this->typeDelivery;
    }

    public function setTypeDelivery(?Delivery $typeDelivery): self
    {
        if($this->typeDelivery != null)
            $this->typeDelivery->setCommand(null);
        $this->typeDelivery = $typeDelivery;
        if($typeDelivery->getCommand() != $this)
            $typeDelivery->setCommand($this);

        return $this;
    }

    /**
     * @return Collection|PieceCommand[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function hasProduct(PieceCommand $pieceCommand): bool
    {
        return $this->products->contains($pieceCommand);
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        if($this->user != $user && $this->user != null)
            $this->user->removeCommand($this);
        $this->user = $user;
        if(!$user->hasCommand($this))
            $user->addCommand($this);

        return $this;
    }

}
