<?php

namespace App\Backend;

use Core\AbstractController;
use Form\ContenuForm;
use Service\Helper;

class ContenuController extends AbstractController
{
    public function index()
    {
        $contenuFlags = [
            'LEFT JOIN' => [
                'table' => 'user',
                'sndTable' => 'contenu',
                'firstTag' => 'id',
                'sndTag' => 'user' 
            ]
        ];

        $contenus = $this->manager->fetchAll('contenu', $contenuFlags);

        return $this->render([
            'contenus' => $contenus
        ]);
    }

    public function update()
    {
        $contenuId = $this->app->routeur()->getBag('id');

        $contenu = $this->manager->findOneBy('contenu', ['WHERE' => 'id = ' . $contenuId]);

        $form = new ContenuForm();

        if ($this->request->hasPost()) {
            $data = $this->request->getAllPost();

            $form->verif($data);

            if (false === $form->isValid()) {
                goto out;
            }

            $data['user'] = $this->app->user()->getId();
            $data['id'] = $contenuId;
            $data['slug'] = Helper::slugify($data['title']);
            if (true !== $this->manager->update('contenu', $data)) {
                $this->notifications->default('500', 'Erreur insertion', $this->manager->getError(), 'danger', $this->isDev());
            }

            $this->notifications->addSuccess('Mise à jour réussie', 'Le contenu a bien été mis à jour.');

            return $this->response->referer();
        }

        out:

        return $this->render([
            'contenu' => $contenu,
            'form' => $form
        ]);
    }

    public function remove()
    {
        $contenuId = $this->app->routeur()->getBag('id');

        $this->manager->remove('contenu', 'id', $contenuId);

        $this->notifications->addSuccess('Suppression réussie', 'Le contenu a bien été supprimé.');

        return $this->response->referer();
    }

    public function new()
    {
        $form = new ContenuForm();

        if ($this->request->hasPost()) {
            $data = $this->request->getAllPost();

            $form->verif($data);

            if (false === $form->isValid()) {
                goto out;
            }

            $data['slug'] = Helper::slugify($data['title']);
            $data['user'] = $this->app->user()->getId();
            if (true !== $this->manager->add('contenu', $data)) {
                $this->notifications->default('500', 'Erreur insertion', $this->manager->getError(), 'danger', $this->isDev());
            }

            $this->notifications->addSuccess('Création réussie', 'Le contenu a bien été ajouté.');

            return $this->response->referer();

        }

        out:

        return $this->render([
            'form' => $form
        ]);
    }
}
