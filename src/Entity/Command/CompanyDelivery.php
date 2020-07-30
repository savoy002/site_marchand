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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="array")
     */
    private $area = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Command\TypeDelivery", mappedBy="company", orphanRemoval=false)
     */
    private $types;

    public function __construct()
    {
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
