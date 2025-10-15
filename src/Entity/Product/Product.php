<?php

namespace App\Entity\Product;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Entity\Product\Category;
use App\Entity\Product\VariantProduct;

use App\Repository\Product\ProductRepository;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: 'Product')]
class Product
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    private $id;

    #[Column(type: 'string', length: 255, name: 'name_prod')]
    private $name;

    #[Column(type: 'string', length: 255, nullable: true, name: 'img_prod')]
    private $imgFileName;

    #[Column(type: 'text', nullable: true, name: 'desc_prod')]
    private $description;

    #[Column(type: 'integer', name: 'stock_prod')]
    private $stock;

    #[Column(type: 'string', length: 255, unique: true, name: 'code_prod')]
    private $code;

    ///**
    // * @ORM\Column(type="boolean", name="activate_prod", options={"default":false})
    // */
    #[Column(type: 'boolean', name: 'activate_prod')]
    private $activate;

    ///**
    // * @ORM\Column(type="boolean", name="deleted_prod", options={"default":false})
    // */
    #[Column(type: 'boolean', name: 'deleted_prod')]
    private $delete;

    ///**
    // * @ORM\ManyToMany(targetEntity="App\Entity\Product\Category", mappedBy="products")
    // */

    #[ManyToMany(targetEntity: Category::class, mappedBy: 'products')]
    private $categories;

    ///**
    // * @ORM\OneToMany(targetEntity="App\Entity\Product\VariantProduct", mappedBy="product", orphanRemoval=false)
    // */

    #[OneToMany(targetEntity: VariantProduct::class, mappedBy: 'product', orphanRemoval: false)]
    private $variantsProducts;

    public function __construct()
    {
        $this->activate = false;
        $this->delete = false;
        $this->categories = new ArrayCollection();
        $this->variantsProducts = new ArrayCollection();
    }

    /**
     *  @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     *  @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     *  @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     *  @return string
     */
    public function getImgFileName(): ?string
    {
        return $this->imgFileName;
    }

    /**
     *  @return self
     */
    public function setImgFileName(string $imgFileName): self
    {
        $this->imgFileName = $imgFileName;

        return $this;
    }

    /**
     *  @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     *  @return self
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     *  @return int
     */
    public function getStock(): ?int
    {
        return $this->stock;
    }

    /**
     *  @return self
     */
    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     *  @return self
     */
    public function calculStock(): self {
        $this->stock = 0;
        foreach($this->variantsProducts as $variant_product) {
            if($variant_product->getActivate())
                $this->stock += $variant_product->getStock();
        }
        return $this;
    }

    /**
     *  @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     *  @return self
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     *  @return bool
     */
    public function getActivate(): bool
    {
        return $this->activate;
    }

    /**
     *  @return self
     */
    public function setActivate(bool $activate): self
    {
        $this->activate = $activate;

        return $this;
    }

    /**
     *  @return bool
     */
    public function getDelete(): bool
    {
        return $this->delete;
    }

    /**
     *  @return self
     */
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

    /**
     *  @return bool
     */
    public function hasCategory(Category $category):bool
    {
        return $this->categories->contains($category);
    }

    /**
     *  @return self
     */
    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            if(!$category->hasProduct($this))
                $category->addProduct($this);
        }

        return $this;
    }

    /**
     *  @return self
     */
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

    /**
     *  @return bool
     */
    public function hasVariantProduct(VariantProduct $variantProduct): bool
    {
        return $this->variantsProducts->contains($variantProduct);
    }

    /**
     *  @return self
     */
    public function addVariantProduct(VariantProduct $variantProduct): self
    {
        if (!$this->hasVariantProduct($variantProduct)) {
            $this->variantsProducts[] = $variantProduct;
            if($variantProduct->getProduct() != $this)
                $variantProduct->setProduct($this);
        }

        return $this;
    }

    /**
     *  @return self
     */
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
