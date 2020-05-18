<?php

namespace App\Entity\Product;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Entity\Product\Product;
use App\Entity\Product\VariantProduct;


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
    private $imgFileName;

    /**
     * @ORM\Column(type="string", length=255, unique=true, name="code_cat")
     */
    private $code;

    /**
     * @ORM\Column(type="boolean", name="activate_cat", options={"default":false})
     */
    private $activate;

    /**
     * @ORM\Column(type="boolean", name="deleted_cat", options={"default":false})
     */
    private $delete;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Product\Product", inversedBy="categories")
     * @ORM\JoinTable(name="CategoriesProducts", 
     *          joinColumns={@ORM\JoinColumn(name="prod_id_cat", referencedColumnName="id")},
     *          inverseJoinColumns={@ORM\JoinColumn(name="cat_id_prod", referencedColumnName="id")})
     */
    private $products;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Product\VariantProduct", inversedBy="categories")
     * @ORM\JoinTable(name="CategoriesVariantsProducts", 
     *          joinColumns={@ORM\JoinColumn(name="prod_var_id_cat", referencedColumnName="id")},
     *          inverseJoinColumns={@ORM\JoinColumn(name="cat_id_prod_var", referencedColumnName="id")})
     */
    private $variantsProducts;

    public function __construct()
    {
        $this->activate = false;
        $this->delete = false;
        $this->products = new ArrayCollection();
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

    public function setImgFileName(?string $imgFileName): self
    {
        $this->imgFileName = $imgFileName;

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
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->addCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            $product->removeCategory($this);
        }

        return $this;
    }
    
    public function getVariantsProducts(): Collection
    {
        return $this->variantsProducts;
    }

    public function addVariantProduct(VariantProduct $variantProduct): self
    {
        if(!$this->variantsProducts->contains($variantProduct)) {
            $this->variantsProducts[] = $variantProduct;
            $variantProduct->addCategory($this);
        }

        return $this;
    }

    public function removeVariantProcut(VariantProduct $variantProduct): self
    {
        if($this->variantsProducts->contains($variantProduct)) {
            $this->variantsProducts->removeElement($variantProduct);
            $variantProduct->removeCategory($this);
        }

        return $this;
    }

}
