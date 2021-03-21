<?php

namespace App\Backend;

use Core\AbstractController;
use Entity\Menu;
use Entity\MenuGroup;
use Form\MenuGroupForm;

class MenugroupController extends AbstractController
{
    public function index()
    {
        $menuGroups = $this->manager->fetchAll('menuGroup');

        /** @var MenuGroup $menuGroup */
        foreach ($menuGroups as $menuGroup) {
            $elements = $this->manager->findBy('menu', ['WHERE' => "menu_group = {$menuGroup->getId()}"]);

            if (null === $elements or false === $elements) {
                continue;
            }

            foreach ($elements as $element) {
                $menuGroup->addElement($element);
            }

        }

        return $this->render([
            'menuGroups' => $menuGroups
        ]);
    }

    public function new()
    {
        $form = new MenuGroupForm();

        if ($this->request->hasPost()) {
            $data = $this->request->getAllPost();

            $form->verif($data);

            if (false === $form->isValid()) {
                goto out;
            }

            $data['author'] = $this->app->user()->getId();

            if (false === $this->manager->add('menuGroup', $data)) {
                $this->notifications->default('500', 'Erreur d\'insertion', $this->manager->getError(), 'danger', $this->isDev());

                goto out;
            }

            $this->notifications->addSuccess('Groupe ajouté', 'Le groupe a correctement été ajouté.');

            return $this->response->referer();
        }

        out:

        return $this->render([
            'form' => $form
        ]);
    }

    public function remove()
    {
        $menuGroupId = $this->app->routeur()->getBag('id');

        $elementDependents = $this->manager->findBy('menu', ['WHERE' => "menu_group = $menuGroupId"]);

        /** @var Menu $element */
        foreach ($elementDependents as $element) {
            $data = [
                'id' => $element->getId(),
                'menu_group' => NULL
            ];

            $this->manager->update('menu', $data);
        }

        $this->manager->remove('menuGroup', 'id', $menuGroupId);

        $this->notifications->addSuccess('Suppression effectuée', 'Le groupe a bien été supprimé.');

        return $this->response->referer();
    }
}