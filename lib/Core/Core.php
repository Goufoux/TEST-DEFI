<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Core;

use Service\Request;
use Config\Developpement;
use Config\Production;
use Entity\User;
use Module\Logger;

abstract class Core
{
    const VERSION = "2.1.3";

    protected $config;
    protected $database;
    protected $user;
    protected $authentification;
    protected $logger;
    protected $routeur;

    public function __construct()
    {
        $this->setConfig();
        $this->setDatabase();
        $this->setAuthentification();
        $this->setLogger();
        $this->setRouteur();
    }

    public function logger()
    {
        return $this->logger;
    }

    public function routeur(): Routeur
    {
        return $this->routeur;
    }

    public function setRouteur()
    {
        $this->routeur = new Routeur($this);

        return $this;
    }

    public function setLogger()
    {
        $this->logger = new Logger($this->getDatabase()->bdd());

        return $this;
    }

    public function getDatabase()
    {
        return $this->database;
    }

    public function authentification()
    {
        return $this->authentification;
    }

    public function auth()
    {
        return $this->authentification;
    }

    public function user(): ?User
    {
        return $this->user;
    }

    public function config()
    {
        return $this->config;
    }
    
    abstract public function run();

    public function setAuthentification()
    {
        $this->authentification = new Authentification($this->database->bdd());

        if ($this->authentification->isAuthentificated()) {
            $this->setUser($this->authentification->getUser());
        }

        return $this;
    }
    
    private function setUser($user = null)
    {
        $this->user = $user;
        
        return $this;
    }

    private function setDatabase()
    {
        $this->database = new Database($this->config);

        return $this;
    }

    private function setConfig()
    {
        $request = new Request;
        $this->config = new Production;
        if ($request->getServerAddr() == '::1' || preg_match('#^127.0.0.1$#', $request->getRemoteAddr())) {
            $this->config = new Developpement;
        }
    }

    public function getVersion()
    {
        return self::VERSION;
    }
}
