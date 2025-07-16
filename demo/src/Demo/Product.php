<?php

namespace Demo;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "products")]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    protected $id;

    #[ORM\Column(type: "string")]
    protected $name;

    #[ORM\Column(type: "datetime", nullable: true)]
    protected $updated;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setUpdated(): void
    {
        // will NOT be saved in the database
        $this->updated = new \DateTime('now');
    }
}
