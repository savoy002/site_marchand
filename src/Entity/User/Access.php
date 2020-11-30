<?php

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;

use DateTime;

/**
 * @ORM\Entity(repositoryClass="App\Repository\User\AccessRepository")
 * @ORM\Table(name="Access")
 */
class Access
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, name="code_acc", unique=true)
     */
    private $code;

    /**
     * @ORM\Column(type="datetime", name="created_at_acc")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean", name="used_acc")
     */
    private $used;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User", inversedBy="accesses")
     * @ORM\JoinColumn(name="id_user_acc", referencedColumnName="id", nullable=false)
     */
    private $user;

    public function __construct() {
        $this->used = false;
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUsed(): ?bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): self
    {
        $this->used = $used;

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

}
