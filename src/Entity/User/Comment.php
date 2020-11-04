<?php

namespace App\Entity\User;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Product\VariantProduct;
use App\Entity\User\User;


/**
 * @ORM\Entity(repositoryClass="App\Repository\User\CommentRepository")
 * @ORM\Table(name="Comment")
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", name="mark_comment", options={"min": 1, "max": 5})
     */
    private $mark;

    /**
     * @ORM\Column(type="text", name="text_comment")
     */
    private $text;

    /**
     * @ORM\Column(type="datetime", name="created_at_comment", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean", name="deleted_comment", options={"default":false})
     */
    private $delete;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User", inversedBy="comments")
     * @ORM\JoinColumn(name="user_id_comment", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product\VariantProduct", inversedBy="comments")
     * @ORM\JoinColumn(name="prod_id_comment", referencedColumnName="id")
     */
    private $product;

    public function __construct() 
    {
        $this->delete = false;
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMark(): ?int
    {
        return $this->mark;
    }

    public function setMark(int $mark): self
    {
        $this->mark = $mark;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getProduct(): ?VariantProduct
    {
        return $this->product;
    }

    public function setProduct(?VariantProduct $product): self
    {
        $this->product = $product;

        return $this;
    }
}
