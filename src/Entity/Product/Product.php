<?php

namespace App\Entity\Product;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Entity\Product\Category;
use App\Entity\Product\VariantProduct;

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
    private $imgFileName;

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
     * @ORM\Column(type="boolean", name="activate_prod", options={"default":false})
     */
    private $activate;

    /**
     * @ORM\Column(type="boolean", name="deleted_prod", options={"default":false})
     */
    private $delete;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Product\Category", mappedBy="products")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product\VariantProduct", mappedBy="product", orphanRemoval=false)
     */
    private $variantsProducts;

    public function __construct()
    {
        $this->activate = false;
        $this->delete = false;
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

    public function getImgFileName(): ?string
    {
        return $this->imgFileName;
    }

    public function setImgFileName(string $imgFileName): self
    {
        $this->imgFileName = $imgFileName;

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

    public function calculStock(): self {
        $this->stock = 0;
        foreach($this->variantsProducts as $variant_product) {
            if($variant_product->getActivate())
                $this->stock += $variant_product->getStock();
        }
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

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function hasCategory(Category $category):bool
    {
        return $this->categories->contains($category);
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            if(!$category->hasProduct($this))
                $category->addProduct($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            if($category->hasProduct($this))
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

    public function hasVariantProduct(VariantProduct $variantProduct): bool
    {
        return $this->variantsProducts->contains($variantProduct);
    }

    public function addVariantProduct(VariantProduct $variantProduct): self
    {
        if (!$this->hasVariantProduct($variantProduct)) {
            $this->variantsProducts[] = $variantProduct;
            if($variantProduct->getProduct() != $this)
                $variantProduct->setProduct($this);
        }

        return $this;
    }

    public function removeVariantProduct(VariantProduct $variantProduct): self
    {
        if ($this->hasVariantProduct($variantProduct)) {
            $this->variantsProducts->removeElement($variantProduct);
            // set the owning side to null (unless already changed)
            if ($variantProduct->getProduct() === $this)
                $variantProduct->setProduct(null);
        }

        return $this;
    }
}
