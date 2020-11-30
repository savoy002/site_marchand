<?php

namespace App\Entity\Command;

use App\Entity\Product\VariantProduct;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Command\PieceCommandRepository")
 * @ORM\Table(name="PieceCommand")
 */
class PieceCommand
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", name="nb_prod")
     */
    private $nbProducts;

    /**
     * @ORM\Column(type="integer", name="mem_price_prod")
     */
    private $priceProduct;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Command\Command", inversedBy="products")
     * @ORM\JoinColumn(name="com_id_piece_com", referencedColumnName="id")
     */
    private $command;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product\VariantProduct", inversedBy="commands")
     * @ORM\JoinColumn(name="prod_id_piece_com", referencedColumnName="id")
     */
    private $product;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbProducts(): ?int
    {
        return $this->nbProducts;
    }

    public function setNbProducts(int $nbProducts): self
    {
        $this->nbProducts = $nbProducts;

        return $this;
    }

    public function getPriceProduct(): ?int
    {
        return $this->priceProduct;
    }

    public function setPriceProduct(int $priceProduct): self
    {
        $this->priceProduct = $priceProduct;

        return $this;
    }

    public function getCommand(): ?Command
    {
        return $this->command;
    }

    public function setCommand(?Command $command): self
    {
        if($this->command != $command && $this->command != null)
            $this->command->removeProduct($this);
        $this->command = $command;
        if(!$command->hasProduct($this))
            $command->addProduct($this);

        return $this;
    }

    public function getProduct(): ?VariantProduct
    {
        return $this->product;
    }

    public function setProduct(?VariantProduct $product): self
    {
        if($this->product != $product && $this->product != null)
            $this->product->removeCommand($this);
        $this->product = $product;
        if(!$product->hasCommand($this))
            $product->addCommand($this);

        return $this;
    }


    public function calculPriceProduct():self
    {
        if(is_null($this->priceProduct) && !is_null($this->product))
            $this->priceProduct = $this->product->getPrice();

        return $this;
    }


}
