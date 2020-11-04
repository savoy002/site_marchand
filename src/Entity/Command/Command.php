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
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $completed;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="date_receive")
     */
    private $dateReceive;

    /**
     * @ORM\Column(type="boolean", name="is_basket")
     */
    private $isBasket;

    /**
     * @ORM\Column(type="integer", name="price_total", nullable=true)
     */
    private $priceTotal;

    /**
     * @ORM\Column(type="boolean", name="deleted_com", options={"default":false})
     */
    private $delete;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Command\Adress", inversedBy="commands")
     * @ORM\JoinColumn(name="adr_id_com", referencedColumnName="id")
     */
    private $placeDel;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Command\Delivery", inversedBy="commands")
     * @ORM\JoinColumn(name="del_id_com", referencedColumnName="id")
     */
    private $delivery;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Command\PieceCommand", mappedBy="command")
     */
    private $products;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User", inversedBy="commands")
     * @ORM\JoinColumn(name="user_id_com", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Command\TypeDelivery", inversedBy="commands")
     * @ORM\JoinColumn(name="type_del_id_com", referencedColumnName="id")
     */
    private $typeDelSelected;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->completed = false;
        $this->isBasket = true;
        $this->priceTotal = null;
        $this->delete = false;
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCompleted(): ?bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): self
    {
        $this->completed = $completed;
        if($completed)
            $this->setIsBasket(false);

        return $this;
    }

    public function getDateReceive(): ?DateTime
    {
        return $this->dateReceive;
    }

    public function setDateReceive(DateTime $dateReceive): self
    {
        if(!$this->delivery !== null) {
            if($this->delivery->getDate() < $dateReceive)
                $this->dateReceive = $dateReceive;
        }

        return $this;
    }

    public function getIsBasket(): bool
    {
        return $this->isBasket;
    }

    public function setIsBasket(bool $isBasket):self
    {
        $this->isBasket = $isBasket;

        return $this;
    }

    public function getPriceTotal(): ?int
    {
        return $this->priceTotal;
    }

    public function calculPriceTotal(): self
    {
        if($this->completed) {

            $this->priceTotal = 0;

            if(!empty($this->products)) {

                foreach ($this->products as $pieceCommand)
                    $this->priceTotal += $pieceCommand->getNbProducts() * $pieceCommand->getProduct()->getPrice();

            }

            if($this->delivery !== null)
                $this->priceTotal += $this->delivery->getType()->getPrice();

        }

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

    public function getPlaceDel(): ?Adress
    {
        return $this->placeDel;
    }

    public function setPlaceDel(?Adress $adress): self
    {
        if($this->placeDel != null)
            $this->placeDel->removeCommand($this);
        $this->placeDel = $adress;
        if($adress !== null) {
            if(!$adress->hasCommand($this))
                $adress->addCommand($this);
        }

        return $this;
    }

    public function getDelivery(): ?Delivery
    {
        return $this->delivery;
    }

    public function setDelivery(?Delivery $delivery): self
    {
        if($this->delivery != null) {
            if($this->delivery->hasCommand($this))
                $this->delivery->removeCommand($this);
        }
        $this->delivery = $delivery;
        if($delivery->getCommand() != $this)
            $delivery->addCommand($this);

        return $this;
    }

    /**
     * @return Collection|PieceCommand[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function isEmptyProduct(): bool
    {
        return $this->products->isEmpty();
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
        if($user != null) {
            if(!$user->hasCommand($this))
                $user->addCommand($this);
        }

        return $this;
    }

    public function getTypeDelSelected(): ?TypeDelivery
    {
        return $this->typeDelSelected;
    }

    public function setTypeDelSelected(?TypeDelivery $typeDelSelected): self
    {
        if($this->typeDelSelected != null && $this->typeDelSelected != $typeDelSelected)
            $this->typeDelSelected->removeCommand($this);
        $this->typeDelSelected = $typeDelSelected;
        if($typeDelSelected !== null) {
            if(!$typeDelSelected->hasCommands($this))
                $typeDelSelected->addCommand($this);
        }

        return $this;
    }



    public function containProduct(VariantProduct $product): bool
    {
        $contain = false;
        foreach($this->getProducts() as $pieceCommand) {
            if($pieceCommand->getProduct() === $product)
                $contain = true;
        }
        return $contain;
    }

}
