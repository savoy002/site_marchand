<?php

namespace App\Entity\Command;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Command\TypeDeliverySelectedRepository")
 * @ORM\Table(name="TypeDeliverySelected")
 */
class TypeDeliverySelected
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", name="mem_price_del")
     */
    private $priceDelivery;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Command\TypeDelivery", inversedBy="commands")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="id")
     */
    private $typeDelivery;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Command\Command", inversedBy="typeDelSelected", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, referencedColumnName="id")
     */
    private $command;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPriceDelivery(): ?int
    {
        return $this->priceDelivery;
    }

    public function setPriceDelivery(int $priceDelivery): self
    {
        $this->priceDelivery = $priceDelivery;

        return $this;
    }

    public function getTypeDelivery(): ?TypeDelivery
    {
        return $this->typeDelivery;
    }

    public function setTypeDelivery(?TypeDelivery $typeDelivery): self
    {
        if($this->typeDelivery != null && $this->typeDelivery != $typeDelivery)
            $this->typeDelivery->removeCommand($this);

        $this->typeDelivery = $typeDelivery;

        if($typeDelivery->hasCommands($this))
            $typeDelivery->addCommands($this);

        $this->calculPriceDelivery();

        return $this;
    }

    public function getCommand(): ?Command
    {
        return $this->command;
    }

    public function setCommand(Command $command): self
    {
        $this->command = $command;

        if($command->getTypeDelSelected() != $this)
            $command->setTypeDelSelected($this);

        return $this;
    }

    
    public function calculPriceDelivery(): self
    {
        if(!is_null($this->typeDelivery))
            $this->priceDelivery = $this->typeDelivery->getPrice();

        return $this;
    }


}
