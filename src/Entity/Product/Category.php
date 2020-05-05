<?php

namespace App\Entity\Product;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Product\CategoryRepository")
 * @ORM\Table(name="Category")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, name="name_cat")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="img_cat")
     */
    private $img;

    /**
     * @ORM\Column(type="string", length=255, unique=true, name="code_cat")
     */
    private $code;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Product\Product", inversedBy="categories")
     * @ORM\JoinTable(name="CategoriesProducts", 
     *          joinColumns={@ORM\JoinColumn(name="prod_id_cat", referencedColumnName="id")},
     *          inverseJoinColumns={@ORM\JoinColumn(name="cat_id_prod", referencedColumnName="id")})
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
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

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): self
    {
        $this->img = $img;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
        }

        return $this;
    }
    
}
