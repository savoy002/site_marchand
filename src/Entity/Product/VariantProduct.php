<?php

namespace App\Entity\Product;

use App\Entity\Command\PieceCommand;
use App\Entity\User\Comment;
use App\Entity\Product\Category;
use App\Entity\Product\Product;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Product\VariantProductRepository")
 * @ORM\Table(name="VariantProduct")
 */
class VariantProduct
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, name="name_var_prod")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="img_var_prod")
     */
    private $imgFileName;

    /**
     * @ORM\Column(type="text", nullable=true, name="desc_var_prod")
     */
    private $description;

    /**
     * @ORM\Column(type="integer", name="stock_var_prod")
     */
    private $stock;

    /**
     * @ORM\Column(type="string", length=255, unique=true, name="code_var_prod")
     */
    private $code;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="boolean", name="activate_var_prod", options={"default":false})
     */
    private $activate;

    /**
     * @ORM\Column(type="boolean", name="deleted_var_prod", options={"default":false})
     */
    private $delete;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product\Product", inversedBy="variantsProducts")
     * @ORM\JoinColumn(name="prod_id_var_prod", referencedColumnName="id")
     */
    private $product;
    
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Product\Category", mappedBy="variantsProducts")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Command\PieceCommand", mappedBy="product")
     */
    private $commands;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User\Comment", mappedBy="product")
     */
    private $comments;

    public function __construct()
    {
        $this->activate = false;
        $this->delete = false;
        $this->stock = 0;
        $this->categories = new ArrayCollection();
        $this->commands = new ArrayCollection();
        $this->comments = new ArrayCollection();
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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price)
    {
        $this->price = $price;

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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        if($this->product != null)
            $this->product->removeVariantProduct($this);
        $this->product = $product;
        if($product != null) {
            if(!$product->hasVariantProduct($this))
                $product->addVariantProduct($this);
        }
        return $this;
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function hasCategory(Category $category): bool
    {
        return $this->categories->contains($category);
    }

    public function addCategory(Category $category): self
    {
        if(!$this->categories->contains($category)) {
            $this->categories[] = $category;
            if(!$category->hasVariantProduct($this))
                $category->addVariantProduct($this);
        }
        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            if($category->hasVariantProduct($this))
                $category->removeVariantProcut($this);
        }
        return $this;
    }

    /**
     * @return Collection|PieceCommand[]
     */
    public function getCommands(): Collection
    {
        return $this->commands;
    }

    public function hasCommand(PieceCommand $pieceCommand): bool
    {
        return $this->commands->contains($pieceCommand);
    }

    public function addCommand(PieceCommand $command): self
    {
        if (!$this->commands->contains($command)) {
            $this->commands[] = $command;
            $command->setProduct($this);
        }

        return $this;
    }

    public function removeCommand(PieceCommand $command): self
    {
        if ($this->commands->contains($command)) {
            $this->commands->removeElement($command);
            // set the owning side to null (unless already changed)
            if ($command->getProduct()->getId() === $this->getId())
                $command->setProduct(null);
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setProduct($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getProduct()->getId() === $this->getId())
                $comment->setProduct(null);
        }

        return $this;
    }
}
