<?php

namespace App\Entity\Product;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Product\ProductRepository")
 * @ORM\Table(name="Product")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, name="name_prod")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="img_prod")
     */
    private $img;

    /**
     * @ORM\Column(type="text", nullable=true, name="desc_prod")
     */
    private $description;

    /**
     * @ORM\Column(type="integer", name="stock_prod")
     */
    private $stock;

    /**
     * @ORM\Column(type="string", length=255, unique=true, name="code_prod")
     */
    private $code;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Product\Category", mappedBy="products")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product\VariantProduct", mappedBy="product", orphanRemoval=true)
     */
    private $variantsProducts;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->variantsProducts = new ArrayCollection();
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

    public function setImg(string $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
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

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addProduct($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            $category->removeProduct($this);
        }

        return $this;
    }

    /**
     * @return Collection|VariantProduct[]
     */
    public function getVariantsProducts(): Collection
    {
        return $this->variantsProducts;
    }

    public function addVariantsProduct(VariantProduct $variantsProduct): self
    {
        if (!$this->variantsProducts->contains($variantsProduct)) {
            $this->variantsProducts[] = $variantsProduct;
            $variantsProduct->setProduct($this);
        }

        return $this;
    }

    public function removeVariantsProduct(VariantProduct $variantsProduct): self
    {
        if ($this->variantsProducts->contains($variantsProduct)) {
            $this->variantsProducts->removeElement($variantsProduct);
            // set the owning side to null (unless already changed)
            if ($variantsProduct->getProduct() === $this) {
                $variantsProduct->setProduct(null);
            }
        }

        return $this;
    }
}
