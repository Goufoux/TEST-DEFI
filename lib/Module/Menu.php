<?php

namespace Module;

use Core\Application;
use Core\Managers;
use Entity\Menu as EntityMenu;

class Menu
{
    private $app;

    public function __construct(Application $application)
    {
        $this->app = $application;
    }

    public function launch()
    {
        $bdd = $this->app->getDatabase()->bdd();
        $manager = new Managers($bdd);

        $elementsFlags = [
            
            'LEFT JOIN' => [
                'table' => 'menuGroup',
                'sndTable' => 'menu',
                'firstTag' => 'id',
                'sndTag' => 'menu_group' 
            ]
        ];

        $elements = $manager->fetchAll('menu', $elementsFlags);

        foreach ($elements as $key => $element) {
            if (null !== $element->getContenu()) {
                $contenu = $manager->findOneBy('contenu', ['WHERE' => "id = {$element->getContenu()}"]);
                $element->setContenu($contenu);
            }
        }

        $menu = $this->build($elements);

        return $menu;
    }

    public function build(array $elements)
    {
        $items = [];

        $groupItems = [];

        $itemBrand = 'Genarkys';

        /** @var EntityMenu $menu */
        foreach ($elements as $key => $menu) {
            if (1 == $menu->getNavbarBrand()) {
                $itemBrand = $menu->getName();
                continue;
            }

            if (null !== $menu->getMenuGroup()->getId()) {
                $groupItems[$menu->getMenuGroup()->getOrdre()][] = $menu;
                continue;
            }

            $items[$menu->getOrdre()] = $menu;
        }

        ksort($items);

        $navbarBrand = '<a href="/" data-toggle="tooltip" title="Retour à l\'accueil" class="navbar-brand">
                            <img src="/img/nav-logo.png" alt="Logo menu" />
                            <span>'.$itemBrand.'</span> 
                        </a>';

        $navbarButton = '<button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse" data-target="#collapsibleNavId" aria-controls="collapsibleNavId"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <i class="fas fa-bars"></i>
                        </button>';

        $divCollapseStart = '<div class="collapse navbar-collapse offset-md-3 offset-sm-5" id="collapsibleNavId">';
        $divCollapseEnd = '</div>';

        $ulNavbarItemStart = '<ul class="navbar-nav mr-auto mt-2 mt-lg-0">';
        $ulNavbarItemEnd = '</ul>';

        $liGroups = [];
        $groups = [];
        $newItems = [];
        foreach ($items as $item) {
            $link = '#';
            if (null !== $item->getContenu()) {
                $link = "/contenu/{$item->getContenu()->getId()}/{$item->getContenu()->getSlug()}";
            }
            if (null !== $item->getLink()) {
                $link = $item->getLink();
            }
            $newItems[$item->getOrdre()] = '<li class="nav-item"><a class="nav-link" href="'.$link.'">'.$item->getName().'</a></li>';
        }

        foreach ($groupItems as $key => $items) {
            $group = '<div class="nav-item navbar-group">';
            foreach ($items as $item) {
                $link = '#';
                if (null !== $item->getContenu()) {
                    $link = "/contenu/{$item->getContenu()->getId()}/{$item->getContenu()->getSlug()}";
                }
                if (null !== $item->getLink()) {
                    $link = $item->getLink();
                }
                $group .= '<a class="nav-link" href="'.$link.'">'.$item->getName().'</a>';
            }
            $group .= '</div>';
            $newItems[$key] = $group;
        }

        $navbar = $navbarBrand.$navbarButton.$divCollapseStart.$ulNavbarItemStart;

        ksort($newItems);

        foreach ($newItems as $item) {
            $navbar .= $item;
        }

        if ($this->app->auth()->hasRole('ROLE_ADMIN') || $this->app->auth()->hasRole('ROLE_SUPER_ADMIN')) {
            $navbar .= '<li class="nav-item"><a href="/admin/" class="nav-link"><i class="fas fa-key"></i></a></li>';
            $navbar .= '<li class="nav-item"><a href="/logout" class="nav-link"><i class="fas fa-sign-out-alt"></i></a></li>';
        }

        $navbar .= $ulNavbarItemEnd.$divCollapseEnd;
            
        return $navbar;
    }

}