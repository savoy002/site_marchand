<?php

namespace App\Entity\Product;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use DateTime;

use App\Entity\Command\PieceCommand;
use App\Entity\User\Comment;
use App\Entity\Product\Category;
use App\Entity\Product\Product;

use App\Repository\Product\VariantProductRepository;

#[ORM\Entity(repositoryClass: VariantProductRepository::class)]
#[ORM\Table(name: 'VariantProduct')]
class VariantProduct
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    private $id;

    #[Column(type: 'string', length: 255, name: 'name_var_prod')]
    private $name;

    #[Column(type: 'string', length: 255, nullable: true, name: 'img_var_prod')]
    private $imgFileName;

    #[Column(type: 'text', nullable: true, name: 'desc_var_prod')]
    private $description;

    #[Column(type: 'integer', name: 'stock_var_prod')]
    private $stock;

    #[Column(type: 'string', length: 255, unique: true, name: 'code_var_prod')]
    private $code;

    #[Column(type: 'integer')]
    private $price;

    ///**
    // * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"}, name="created_at_var_prod")
    // */

    #[Column(type: 'datetime', name: 'created_at_var_prod')]
    private $createdAt;

    ///**
    // * @ORM\Column(type="boolean", options={"default": false}, name="is_wellcome_var_prod")
    // */

    #[Column(type: 'boolean', name: 'is_wellcome_var_prod')]
    private $isWellcome;

    ///**
    // * @ORM\Column(type="boolean", name="activate_var_prod", options={"default":false})
    // */

    #[Column(type: 'boolean', name: 'activate_var_prod')]
    private $activate;

    ///**
    // * @ORM\Column(type="boolean", name="deleted_var_prod", options={"default":false})
    // */
    
    #[Column(type: 'boolean', name: 'deleted_var_prod')]
    private $delete;

    ///**
    // * @ORM\ManyToOne(targetEntity="App\Entity\Product\Product", inversedBy="variantsProducts")
    // * @ORM\JoinColumn(name="prod_id_var_prod", referencedColumnName="id")
    // */

    #[ManyToOne(targetEntity: Product::class, inversedBy: 'variantsProducts')]
    #[JoinColumn(name: 'prod_id_var_prod', referencedColumnName: 'id')]
    private $product;
    
    ///**
    // * @ORM\ManyToMany(targetEntity="App\Entity\Product\Category", mappedBy="variantsProducts")
    // */

    #[ManyToMany(targetEntity: Category::class, mappedBy: 'variantsProducts')]
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
        $this->createdAt = new DateTime();
        $this->isWellcome = false;
        $this->activate = false;
        $this->delete = false;
        $this->stock = 0;
        $this->categories = new ArrayCollection();
        $this->commands = new ArrayCollection();
        $this->comments = new ArrayCollection();
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
    public function setImgFileName(?string $imgFileName): self
    {
        $this->imgFileName = $imgFileName;

        return $this;
    }

    /**
     *  @return strin
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
     *  @return int
     */
    public function getPrice(): ?int
    {
        return $this->price;
    }

    /**
     *  @return self
     */
    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     *  @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     *  @return self
     */
    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     *  @return bool
     */
    public function getIsWellcome(): bool
    {
        return $this->isWellcome;
    }

    /**
     *  @return self
     */
    public function setIsWellcome(bool $isWellcome): self
    {
        $this->isWellcome = $isWellcome;

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
     *  @return Product
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     *  @return self
     */
    public function setProduct(?Product $product): self
    {
        if($this->product != null)
            $this->product->removeVariantProduct($this);
        $this->product = $product;
        if($product != null)
            $product->addVariantProduct($this);

        return $this;
    }

    /**
     *  @return Collection
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     *  @return bool
     */
    public function hasCategory(Category $category): bool
    {
        return $this->categories->contains($category);
    }

    /**
     *  @return self
     */
    public function addCategory(Category $category): self
    {
        if(!$this->categories->contains($category)) {
            $this->categories[] = $category;
            if(!$category->hasVariantProduct($this))
                $category->addVariantProduct($this);
        }
        return $this;
    }

    /**
     *  @return self
     */
    public function removeCategory(Category $category): self
    {
        if($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            if($category->hasVariantProduct($this))
                $category->removeVariantProduct($this);
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

    /**
     *  @return bool
     */
    public function hasCommand(PieceCommand $pieceCommand): bool
    {
        return $this->commands->contains($pieceCommand);
    }

    /**
     *  @return self
     */
    public function addCommand(PieceCommand $command): self
    {
        if (!$this->commands->contains($command)) {
            $this->commands[] = $command;
            $command->setProduct($this);
        }

        return $this;
    }

    /**
     *  @return self
     */
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

    /**
     *  @return self
     */
    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setProduct($this);
        }

        return $this;
    }

    /**
     *  @return self
     */
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
