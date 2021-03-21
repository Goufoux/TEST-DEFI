<?php

namespace App\Backend;

use Core\AbstractController;
use Entity\Menu;
use Form\MenuForm;

class MenuController extends AbstractController
{
    public function index()
    {
        $elementsFlags = [
            'LEFT JOIN' => [
                'table' => 'menuGroup',
                'sndTable' => 'menu',
                'firstTag' => 'id',
                'sndTag' => 'menu_group' 
            ]
        ];

        $elements = $this->manager->fetchAll('menu', $elementsFlags);

        $brand = null;

        $singles = [];

        $groups = [];

        /** @var Menu $element */
        foreach ($elements as $key => $element) {
            if (1 == $element->getNavbarBrand()) {
                $brand = $element;
                continue;
            }

            if (null !== $element->getMenuGroup()->getId()) {
                $singles[$element->getMenuGroup()->getOrdre()]['element'][] = $element; 
                $singles[$element->getMenuGroup()->getOrdre()]['menuGroup'] = true;

                continue;
            }
            $singles[$element->getOrdre()]['element'][] = $element;
        }
        ksort($singles);

        if ($this->request->hasPost()) {
            $data = $this->request->getAllPost();
            if (isset($data['menuGroup'])) {
                $table = 'menuGroup';
                $data['id'] = $data['menuGroup'];
                unset($data['menuGroup']);
            } else {
                $table = 'menu';
                $data['id'] = $data['menu'];
                unset($data['menu']);
            }
            if (false === $this->manager->update($table, $data)) {
                $this->notifications->default('500', 'Erreur', $this->manager->getError(), 'danger', $this->isDev());

                goto out;
            }

            return $this->response->referer();
        }

        out:
        // var_dump($singles);
        return $this->render([
            'elements' => $elements,
            'singles' => $singles,
            'groups' => $groups,
            'brand' => $brand
        ]);
    }

    public function update()
    {
        $elementsFlags = [
            'LEFT JOIN' => [
                'table' => 'menuGroup',
                'sndTable' => 'menu',
                'firstTag' => 'id',
                'sndTag' => 'menu_group' 
            ]
        ];
        $menuId = $this->app->routeur()->getBag('id');
        $menuGroups = $this->manager->fetchAll('menuGroup');
        $contenus = $this->manager->fetchAll('contenu');

        $form = new MenuForm();

        $menu = $this->manager->findOneBy('menu', ['WHERE' => 'id = ' . $menuId], $elementsFlags);

        if ($this->request->hasPost()) {
            $data = $this->request->getAllPost();
            $form->verif($data);
            if (false === $form->isValid()) {
                goto out;
            }

            if (null !== $menu->getLink() && empty($data['link'])) {
                $data['link'] = NULL;
            }

            $data['author'] = $this->app->user()->getId();
            $data['id'] = $menuId;

            if (isset($data['navbar_brand'])) {
                $data['navbar_brand'] = 1;
            } else {
                $data['navbar_brand'] = 0;
            }

            if (false === $this->manager->update('menu', $data)) {
                $this->notifications->default('500', 'Erreur d\'insertion', $this->manager->getError(), 'danger', $this->isDev());

                goto out;
            }

            $this->notifications->addInfo('Mise à jour réussie', 'L\'élément a bien été mis à jour.');

            return $this->response->referer();

        }

        out:

        return $this->render([
            'menu' => $menu,
            'menuGroups' => $menuGroups,
            'contenus' => $contenus,
            'form' => $form
        ]);
    }

    public function removegroup()
    {
        $menuId = $this->app->routeur()->getBag('id');

        $data['menu_group'] = NULL;
        $data['id'] = $menuId;

        $this->manager->update('menu', $data);

        return $this->response->referer();
    }

    public function removecontenu()
    {
        $menuId = $this->app->routeur()->getBag('id');

        $data['contenu'] = NULL;
        $data['id'] = $menuId;

        $this->manager->update('menu', $data);

        return $this->response->referer();
    }

    public function remove()
    {
        $menuId = $this->app->routeur()->getBag('id');

        $this->manager->remove('menu', 'id', $menuId);

        $this->notifications->addSuccess('Élément supprimé', 'L\'élément a correctement été supprimé.');

        return $this->response->referer();
    }

    public function new()
    {
        $menuGroups = $this->manager->fetchAll('menuGroup');
        $contenus = $this->manager->fetchAll('contenu');

        $form = new MenuForm();

        if ($this->request->hasPost()) {
            $data = $this->request->getAllPost();

            $form->verif($data);

            if (false === $form->isValid()) {
                goto out;
            }

            $data['author'] = $this->app->user()->getId();

            if (isset($data['navbar_brand'])) {
                $data['navbar_brand'] = 1;
            } else {
                $data['navbar_brand'] = 0;
            }

            if (false === $this->manager->add('menu', $data)) {
                $this->notifications->default('500', 'Erreur d\'insertion', $this->manager->getError(), 'danger', $this->isDev());

                goto out;
            }

            $this->notifications->addSuccess('Élément ajouté', 'L\'élément a correctement été ajouté.');

            return $this->response->referer();

        }

        out:

        return $this->render([
            'menuGroups' => $menuGroups,
            'contenus' => $contenus,
            'form' => $form
        ]);
    }
}
