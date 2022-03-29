<?php

namespace App\Entity\User;

use App\Entity\Command\CompanyDelivery;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Table;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

use DateTime;

use App\Entity\Command\Address;
use App\Entity\Command\Command;
use App\Entity\Product\VariantProduct;
use App\Entity\User\Comment;

#version 6.
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


/**
 * @ORM\Entity(repositoryClass="App\Repository\User\UserRepository")
 * @ORM\Table(name="User")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * @ORM\Column(type="datetime", name="created_at_user", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean", name="valid_user", options={"default":false})
     */
    private $valid;

    /**
     * @ORM\Column(type="boolean", name="deleted_user", options={"default":false})
     */
    private $delete;

    /**
     * @ORM\Column(type="boolean", name="admin_user", options={"default":false})
     */
    private $admin;

    /**
     * @ORM\Column(type="boolean", name="super_admin", options={"default":false})
     */
    private $superAdmin;

    /**
     * @ORM\Column(type="string", name="img", unique=true, nullable=true)
     */
    private $imgFileName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Command\Address", inversedBy="belongs")
     * @ORM\JoinColumn(name="adr_id_user", referencedColumnName="id")
     */
    private $live;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User\Comment", mappedBy="user", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Command\Command", mappedBy="user", orphanRemoval=false)
     */
    private $commands;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User\Access", mappedBy="user", orphanRemoval=true)
     */
    private $accesses;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Command\CompanyDelivery", inversedBy="owner", cascade={"persist", "remove"})
     */
    private $companyDelivery;
    

    public function __construct() {
        $this->createdAt = new DateTime();
        $this->valid = false;
        $this->delete = false;
        $this->admin = false;
        $this->superAdmin = false;
        $this->comments = new ArrayCollection();
        $this->commands = new ArrayCollection();
        $this->accesses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        //if(sizeof($roles) === 0)
        //    $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(String $email): self
    {
        $this->email = $email;
        return $this;   
    }

    public function getCreateAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getValid(): bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): self 
    {
        $this->valid = $valid;
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

    public function getAdmin(): bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): self
    {
        $this->admin = $admin;
        return $this;
    }

    public function getSuperAdmin(): bool
    {
        return $this->superAdmin;
    }

    public function setSuperAdmin(bool $superAdmin): self
    {
        $this->superAdmin = $superAdmin;
        $this->setRoles(["ROLE_ADMIN"]);
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

    public function getLive(): ?Address
    {
        return $this->live;
    }

    public function setLive(?Address $address): self 
    {
        if($this->live !== null)
            $this->live->removeBelong($this);
        $this->live = $address;
        if($address != null)
            $address->addBelong($this);

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
            if($comment->getUser() != $this)
                $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    public function getCommands(): Collection
    {
        return $this->commands;
    }

    public function hasCommand(Command $command): bool
    {
        return $this->commands->contains($command);
    }

    public function addCommand(Command $command): self 
    {
        if(!$this->hasCommand($command)) {
            $this->commands[] = $command;
            if($command->getUser() != $this)
                $command->setUser($this);
        }

        return $this;
    }

    public function removeCommand(Command $command): self
    {
        if($this->hasCommand($command)) {
            $this->commands->removeElement($command);
            if($command->getUser() === $this)
                $command->setUser(null);
        }

        return $this;
    }

    /**
     * @return Collection|Access[]
     */
    public function getAccesses(): Collection
    {
        return $this->accesses;
    }

    public function addAccess(Access $access): self
    {
        if (!$this->accesses->contains($access)) {
            $this->accesses[] = $access;
            
            if($access->getUser() != $this)
                $access->setUser($this);
        }

        return $this;
    }

    public function removeAccess(Access $access): self
    {
        if ($this->accesses->contains($access)) {
            $this->accesses->removeElement($access);
            // set the owning side to null (unless already changed)
            if ($access->getUser() === $this) {
                $access->setUser(null);
            }
        }

        return $this;
    }

    public function getCompanyDelivery(): ?CompanyDelivery
    {
        return $this->companyDelivery;
    }

    public function setCompanyDelivery(?CompanyDelivery $companyDelivery): self
    {
        if(!is_null($companyDelivery)) {
            if($companyDelivery->getOwner() != $this)
                $companyDelivery->setOwner(null);
        }
        if($this->getRoles() === ["ROLE_COMPANY_ADMIN"]){
            $this->companyDelivery = $companyDelivery;
            if($this->companyDelivery != $this) {
                $companyDelivery->setOwner($this);
            }
        } else if($this->companyDelivery === $companyDelivery && !is_null($companyDelivery)) {
            $this->companyDelivery = null;
        }

        return $this;
    }





    public function getBasket(): ?Command
    {
        $basket = null;
        foreach ($this->commands as $command) {
            if($command->getIsBasket())
                $basket = $command;
        }
        return $basket;
    }

    public function didBuyProduct(VariantProduct $product): bool
    {
        $contain = false;
        foreach ($this->getCommands() as $command) {
            if(!is_null($command->getDateReceive())) {
                if($command->containProduct($product))
                    $contain = true;
            }
        }
        return $contain;
    }

    public function isAlreadyComment(VariantProduct $product): bool
    {
        $contain = false;
        foreach($this->getComments() as $comment) {
            if($comment->getProduct() === $product)
                $contain = true;
        }
        return $contain;
    }

}
