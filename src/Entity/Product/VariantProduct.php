<?php

namespace App\Entity\Product;

use App\Entity\Command\PieceCommand;
use App\Entity\User\Comment;
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
    private $img;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Product\Product", inversedBy="variantsProducts")
     * @ORM\JoinColumn(name="prod_id_prod_var", referencedColumnName="id")
     */
    private $product;

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

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): self
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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Collection|PieceCommand[]
     */
    public function getCommands(): Collection
    {
        return $this->commands;
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
            if ($command->getProduct() === $this) {
                $command->setProduct(null);
            }
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
            if ($comment->getProduct() === $this) {
                $comment->setProduct(null);
            }
        }

        return $this;
    }
}
