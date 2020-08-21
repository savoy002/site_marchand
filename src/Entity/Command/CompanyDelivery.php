<?php

namespace App\Entity\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Command\CompanyDeliveryRepository")
 * @ORM\Table(name="CompanyDelivery")
 */
class CompanyDelivery
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, name="name_comp_del")
     */
    private $name;

    /**
     * @ORM\Column(type="json")
     */
    private $area = [];

    /**
     * @ORM\Column(type="string", name="logo", unique=true, nullable=true)
     */
    private $logoFileName;

    /**
     * @ORM\Column(type="boolean", name="activate_comp_del", options={"default":false})
     */
    private $activate;

    /**
     * @ORM\Column(type="boolean", name="deleted_comp_del", options={"default":false})
     */
    private $delete;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Command\TypeDelivery", mappedBy="company", orphanRemoval=false)
     */
    private $types;

    public function __construct()
    {
        $this->activate = false;
        $this->delete = false;
        $this->types = new ArrayCollection();
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

    public function getArea(): ?array
    {
        return $this->area;
    }

    public function setArea(array $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function getLogoFileName(): ?string
    {
        return $this->logoFileName;
    }

    public function setLogoFileName(string $logoFileName): self
    {
        $this->logoFileName = $logoFileName;

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
     * @return Collection|Delivery[]
     */
    public function getTypes(): Collection
    {
        return $this->types;
    }

    public function hasType(TypeDelivery $type): bool
    {
        return $this->types->contains($type);
    }

    public function addType(TypeDelivery $type): self
    {
        if (!$this->hasType($type)) {
            $this->types[] = $type;
            $type->setCompany($this);
        }

        return $this;
    }

    public function removeType(TypeDelivery $type): self
    {
        if ($this->hasType($type)) {
            $this->types->removeElement($type);
            // set the owning side to null (unless already changed)
            if ($types->getCompany() === $this)
                $delivery->setCompany(null);
        }

        return $this;
    }
}
