<?php

namespace Core;

use Service\MyArray;

class Routeur
{
    const DELIMITER_START = '{';
    const DELIMITER_END = '}';

    private $app;
    private $route;
    private $bag;
    private $match;
    private $controllerClass;
    private $view;
    private $controllerPath;
    private $error;
    private $routeName;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->route = new Route();
    }

    public function run()
    {
        if (false === $this->match()) {
            return false;
        }

        if (false === $this->setControllerClass()) {
            return false;
        }


        if (false === $this->setView()) {
            $this->setError('vue non trouvée.');
            return false;
        }
    }

    public function setControllerClass()
    {
        if (3 !== count($this->getRouteNameElements())) {
            return false;
        }

        $controllerName = $this->getRouteNameElements()[1];

        $interface = $this->route->getInterface();

        $controllerPath = __DIR__.'/../../App/'.ucfirst($interface).'/'.ucfirst($controllerName).'Controller.php';

        if (false === file_exists($controllerPath)) {
            $this->setError('Class path not found : ' . $controllerPath);
            return false;
        }
        
        $this->setControllerPath($controllerPath);

        $controllerClass = 'App\\'.ucfirst($interface).'\\'.ucfirst($controllerName).'Controller';

        if (false === class_exists($controllerClass)) {
            $this->setError('Class not found : ' . $controllerClass);
            return false;
        }

        $this->controllerClass = $controllerClass;

        return true;
    }

    private function getRouteNameElements()
    {
        $routeName = $this->match['name'];

        $this->setRouteName($routeName);
        
        $routeNameElements = explode('_', $routeName);

        return $routeNameElements;
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function setRouteName(?string $routeName): self
    {
        $this->routeName = $routeName;

        return $this;
    }

    public function getControllerClass()
    {
        return $this->controllerClass;
    }

    public function getView()
    {
        return $this->view;
    }

    private function setView()
    {
        $viewName = $this->getRouteNameElements()[2];

        if (false === method_exists($this->controllerClass, $viewName)) {
            return false;
        }

        $this->view = $viewName;
        
        return true;
    }

    private function setControllerPath($controllerPath)
    {
        $this->controllerPath = $controllerPath;

        return $this;
    }

    public function isResource(): bool
    {
        if (preg_match('/\.(?:png|jpg|jpeg|gif)$/', $this->getRoute()->getRequest()->getRequestUri())) {
            return true;
        }

        return false;
    }

    public function match()
    {
        if (true === $this->isResource()) {
            return false;
        }

        if (false === $this->route->getRoutes()) {
            die('Erreur, aucune route');
        }
        $collection = $this->getRoutesCollection();
        if (false === $this->route->getElements()) {
            $this->match = $collection['frontend']['/'];
            
            return true;
        }
        
        $requestElements = $this->route->getElements();

        $match = false;

        $compteur = 0;

        foreach ($collection[$this->route->getInterface()] as $collectionRoute => $data) {
            $compteur = 0;
            $collectionElements = MyArray::clearArray(MyArray::stringToArray($collectionRoute));

            if (count($collectionElements) !== count($requestElements)) {
                continue;
            }
            for ($i = 0; $i < count($collectionElements); $i++) {
                if ($i == 0 && $collectionElements[0] !== $requestElements[0]) {
                    continue;
                }
                if (false === $this->isSpecialItem($collectionElements[$i]) && $collectionElements[$i] !== $requestElements[$i]) {
                    continue;
                }
                
                if (true === $this->isSpecialItem($collectionElements[$i]) && false === $this->matchSpecialItem($requestElements[$i], $collectionElements[$i], $data)) {
                    continue;
                }
                $compteur++;
                $match = $collectionElements;

            }
            if (false !== $match && $compteur === count($requestElements)) {
                $this->match = $data;
                break;
            }
        }

        if (false === $match) {
            $this->setError('Route non trouvée : ' . $this->route->getRequest()->getRequestUri());
            return false;
        }

        return true;
    }

    public function getMatch()
    {
        return $this->match;
    }

    private function addBag(array $bag)
    {
        $this->bag[] = $bag;
    }

    public function getBag(string $key = null)
    {
        if (null !== $key) {
            
            foreach ($this->bag as $elementsNumber => $elements) {
                foreach ($elements as $tag => $value) {
                    if ($tag === $key) {
                        return $value;
                    }
                }
            }
            
            return null;
        }

        return $this->bag;
    }

    private function matchSpecialItem($str, $specialItem, $collection)
    {
        $specialItem = str_replace(self::DELIMITER_START, '', $specialItem);
        $specialItem = str_replace(self::DELIMITER_END, '', $specialItem);
        if (false === $specialItemType = array_search($specialItem, $collection)) {
            return false;
        }
        
        $this->addBag([$specialItem => $str]);

        return true;
    }

    private function isSpecialItem(string $item)
    {
        $firstCharacter = $item[0];
        $lastCharacter = $item[strlen($item)-1];
        
        return self::DELIMITER_START === $firstCharacter && self::DELIMITER_END === $lastCharacter;
    }

    private function getRoutesCollection()
    {
        return $this->route->getRoutes();
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function getError()
    {
        return $this->error;
    }

    private function setError(string $error)
    {
        $this->error = $error;

        return $this;
    }
}