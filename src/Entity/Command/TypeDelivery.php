<?php

namespace App\Entity\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Command\TypeDeliveryRepository")
 * @ORM\Table(name="TypeDelivery")
 */
class TypeDelivery
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
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     */
    private $timeMin;

    /**
     * @ORM\Column(type="integer")
     */
    private $timeMax;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Command\Delivery", mappedBy="type")
     */
    private $deliveries;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Command\CompanyDelivery", inversedBy="types")
     * @ORM\JoinColumn(nullable=false, name="comp_del_id_del", referencedColumnName="id")
     */
    private $company;

    public function __construct()
    {
        $this->deliveries = new ArrayCollection();
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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getTimeMin(): ?int
    {
        return $this->timeMin;
    }

    public function setTimeMin(int $timeMin): self
    {
        $this->timeMin = $timeMin;

        return $this;
    }

    public function getTimeMax(): ?int
    {
        return $this->timeMax;
    }

    public function setTimeMax(int $timeMax): self
    {
        $this->timeMax = $timeMax;

        return $this;
    }

    public function getDeliveries(): Collection
    {
        return $this->deliveries;
    }

    public function hasDelivery(Delivery $delivery): bool
    {
        return $this->deliveries->contains($delivery);
    }

    public function addDelivery(Delivery $delivery): self
    {
        if(!$this->hasDelivery($delivery)) {
            $this->delivery[] = $delivery;

            if($delivery->getType() != $this)
                $delivery->setType($this);
        }

        return $this;
    }

    public function removeDelivery(Delivery $delivery): self
    {
        if($this->hasDelivery($delivery)) {
            $this->delivery->remove($delivery);

            if($delivery->getType() === $this)
                $delivery->setType(null);
        }

        return $this;
    }

    public function getCompany(): CompanyDelivery
    {
        return $this->company;
    }

    public function setCompany(CompanyDelivery $company): self
    {
        $this->company = $company;

        if(!$company->hasType($this))
            $company->addType($this);

        return $this;
    }

}
