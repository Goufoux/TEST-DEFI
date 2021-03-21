<?php

namespace Core;

class Page
{
    public function generate(Application $app)
    {
        $controllerClass = $app->routeur()->getControllerClass();
    
        $controller = new $controllerClass($app);

        $view = $app->routeur()->getView();

        echo $controller->$view();
    }
}