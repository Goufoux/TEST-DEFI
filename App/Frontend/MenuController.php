<?php

namespace App\Frontend;

use Core\AbstractController;
use Module\Menu;

class MenuController extends AbstractController
{
    public function index()
    {
        $moduleMenu = new Menu($this->app);
        $menu = $moduleMenu->launch();

        return $menu;
    }
}
