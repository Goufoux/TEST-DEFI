<?php

namespace Entity;

use Core\Entity;

class ContactDevis extends Entity
{
    const EMBALLAGE = [
        1 => 'Boîte',
        2 => 'Étui',
        3 => 'Fond et couvercle',
        4 => 'Tiroir et fourreau',
        5 => 'Barquette',
        6 => 'Pochette',
        7 => 'Valisette',
        8 => 'Boîte présentoir',
        9 => 'PLV',
        10 => 'Calage',
        11 => 'Plaque',
        12 => 'Autre',
        13 => 'Ne sais pas'
    ];

    const FINITION = [
        1 => 'Sans impression (neutre)',
        2 => 'Impression quadri',
        3 => 'Impression 2 couleurs',
        4 => 'Impression 1 couleur',
        5 => 'Autre'
    ];

    private $id;
    private $createdAt;
    private $productWeight;
    private $dimLongueur;
    private $dimLargeur;
    private $dimHauteur;
    private $finition;
    private $typeEmballage;
    private $quantity;
    private $annualNeed;
    private $path;

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of createdAt
     */ 
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */ 
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of productWeight
     */ 
    public function getProductWeight()
    {
        return $this->productWeight;
    }

    /**
     * Set the value of productWeight
     *
     * @return  self
     */ 
    public function setProductWeight($productWeight)
    {
        $this->productWeight = $productWeight;

        return $this;
    }

    /**
     * Get the value of dimLongueur
     */ 
    public function getDimLongueur()
    {
        return $this->dimLongueur;
    }

    /**
     * Set the value of dimLongueur
     *
     * @return  self
     */ 
    public function setDimLongueur($dimLongueur)
    {
        $this->dimLongueur = $dimLongueur;

        return $this;
    }

    /**
     * Get the value of dimLargeur
     */ 
    public function getDimLargeur()
    {
        return $this->dimLargeur;
    }

    /**
     * Set the value of dimLargeur
     *
     * @return  self
     */ 
    public function setDimLargeur($dimLargeur)
    {
        $this->dimLargeur = $dimLargeur;

        return $this;
    }

    /**
     * Get the value of dimHauteur
     */ 
    public function getDimHauteur()
    {
        return $this->dimHauteur;
    }

    /**
     * Set the value of dimHauteur
     *
     * @return  self
     */ 
    public function setDimHauteur($dimHauteur)
    {
        $this->dimHauteur = $dimHauteur;

        return $this;
    }

    /**
     * Get the value of finition
     */ 
    public function getFinition()
    {
        return self::FINITION[$this->finition];
    }

    /**
     * Set the value of finition
     *
     * @return  self
     */ 
    public function setFinition($finition)
    {
        $this->finition = $finition;

        return $this;
    }

    /**
     * Get the value of typeEmballage
     */ 
    public function getTypeEmballage()
    {
        return self::EMBALLAGE[$this->typeEmballage];
    }

    /**
     * Set the value of typeEmballage
     *
     * @return  self
     */ 
    public function setTypeEmballage($typeEmballage)
    {
        $this->typeEmballage = $typeEmballage;

        return $this;
    }

    /**
     * Get the value of quantity
     */ 
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set the value of quantity
     *
     * @return  self
     */ 
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get the value of annualNeed
     */ 
    public function getAnnualNeed()
    {
        return $this->annualNeed;
    }

    /**
     * Set the value of annualNeed
     *
     * @return  self
     */ 
    public function setAnnualNeed($annualNeed)
    {
        $this->annualNeed = $annualNeed;

        return $this;
    }

    /**
     * Get the value of path
     */ 
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the value of path
     *
     * @return  self
     */ 
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }
}
