<?php

namespace Core;

class Routes
{
    const FILENAME = 'routes.php';

    private $routes;
    private $path;
    
    public function init()
    {
        if (false === $this->loadRoutesFile()) {
            return false;
        }

        if (false === $this->loadRoutes()) {
            return false;
        }

        return true;
    }

    private function loadRoutesFile()
    {
        $path = __DIR__.'/../../config/'.self::FILENAME;

        if (false === file_exists($path)) {
            return false;
        }
        
        $this->path = $path;

        return true;
    }

    private function loadRoutes()
    {
        include $this->path;

        if (false === isset($routes)) {
            $this->routes = false;

            return false;
        }

        $this->routes = $routes;

        return true;
    }

    public function getRoutes()
    {
        return $this->routes;
    }
}