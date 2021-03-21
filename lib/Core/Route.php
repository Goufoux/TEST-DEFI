<?php

namespace Core;

use Service\Request;
use Service\MyArray;

class Route
{
    const INTERFACE_URI = 'admin';

    private $request;
    private $interface;
    private $elements;
    private $routes;
    
    public function __construct()
    {
        $this->request = new Request();
        $this->init();
    }

    public function init()
    {
        $this->setElements();
        $this->setInterface();
        $this->setRoutes();
    }

    public function setRoutes()
    {
        $routes = new Routes();

        if (false === $routes->init()) {
            $this->routes = false;

            return;
        }

        $this->routes = $routes->getRoutes();
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    private function setElements()
    {
        $request = $this->request;

        $elements = MyArray::clearArray(MyArray::stringToArray($request->getRequestUri()));

        if (true === empty($elements)) {
            $this->elements = false;

            return $this;
        }

        $lastElement = $elements[count($elements)-1];

        $clearLastElement = MyArray::stringToArray($lastElement, '?');

        $elements[count($elements)-1] = $clearLastElement[0];

        $this->elements = $elements;

        return $this;
    }

    public function getElements()
    {
        return $this->elements;
    }

    private function setInterface()
    {
        if (false === $this->elements || false === is_array($this->elements)) {
            $this->interface = 'frontend';

            return $this;
        }

        if (self::INTERFACE_URI === $this->elements[0]) {
            $this->interface = 'backend';

            return $this;
        }

        $this->interface = 'frontend';

        return $this;
    }

    public function getInterface()
    {
        return $this->interface;
    }

    public function getRequest()
    {
        return $this->request;
    }
}