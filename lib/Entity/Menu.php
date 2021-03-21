<?php

namespace Entity;

use Core\Entity;

class Menu extends Entity
{
    private $id;
    private $createdAt;
    private $updatedAt;
    private $name;
    private $author;
    private $navbarBrand;
    private $menuGroup;
    private $ordre;
    private $contenu;
    private $link;

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
     * Get the value of updatedAt
     */ 
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @return  self
     */ 
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of author
     */ 
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set the value of author
     *
     * @return  self
     */ 
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get the value of navbarBrand
     */ 
    public function getNavbarBrand()
    {
        return $this->navbarBrand;
    }

    /**
     * Set the value of navbarBrand
     *
     * @return  self
     */ 
    public function setNavbarBrand($navbarBrand)
    {
        $this->navbarBrand = $navbarBrand;

        return $this;
    }

    /**
     * Get the value of ordre
     */ 
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * Set the value of ordre
     *
     * @return  self
     */ 
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get the value of menuGroup
     */ 
    public function getMenuGroup()
    {
        return $this->menuGroup;
    }

    /**
     * Set the value of menuGroup
     *
     * @return  self
     */ 
    public function setMenuGroup($menuGroup)
    {
        $this->menuGroup = $menuGroup;

        return $this;
    }

    /**
     * Get the value of contenu
     */ 
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set the value of contenu
     *
     * @return  self
     */ 
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get the value of link
     */ 
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set the value of link
     *
     * @return  self
     */ 
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }
}
