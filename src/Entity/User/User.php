<?php

namespace App\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Table;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

use DateTime;

use App\Entity\Command\Adress;
use App\Entity\Command\Command;
use App\Entity\User\Comment;


/**
 * @ORM\Entity(repositoryClass="App\Repository\User\UserRepository")
 * @ORM\Table(name="User")
 */
class User implements UserInterface
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Command\Adress", inversedBy="belongs")
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

    public function __construct() {
        $this->createdAt = new DateTime();
        $this->valid = false;
        $this->delete = false;
        $this->admin = false;
        $this->superAdmin = false;
        $this->comments = new ArrayCollection();
        $this->commands = new ArrayCollection();
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
        if($this->getRoles() === "ROLE_USER") 
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

    public function getLive(): ?Adress
    {
        return $this->live;
    }

    public function setLive(?Adress $adress): self 
    {
        if($this->live !== null)
            $this->live->removeBelong($this);
        $this->live = $adress;
        if($adress != null)
            $adress->addBelong($this);

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



}
