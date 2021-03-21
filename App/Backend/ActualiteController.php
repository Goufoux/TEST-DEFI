<?php

namespace App\Backend;

use Core\AbstractController;
use Form\ActualiteForm;
use Service\FileManagement;
use Service\Helper;

class ActualiteController extends AbstractController
{
    public function index()
    {
        $actualiteFlags = [
            'LEFT JOIN' => [
                'table' => 'user',
                'sndTable' => 'actualite',
                'firstTag' => 'id',
                'sndTag' => 'user' 
            ],
            'WHERE' => [
                'table' => 'actualite',
                'tag' => 'archive',
                'value' => '0'
            ],
            'ORDER BY' => [
                'table' => 'actualite',
                'tag' => 'event_date',
                'type' => 'DESC'
            ]
        ];
        $actualites = $this->manager->fetchAll('actualite', $actualiteFlags);

        return $this->render([
            'actualites' => $actualites
        ]);
    }

    public function new()
    {
        $form = new ActualiteForm();

        if ($this->request->hasPost()) {
            $data = $this->request->getAllPost();

            $fileGestion = new FileManagement();
            $image = $_FILES['image'] ?? null;
            
            
            if (null !== $image && $image['error'] !== 4) {
                if (false === $fileGestion->uploadFile($_FILES['image'], $_FILES['image']['name'], 'img', 'actualite')) {
                    $this->notifications->addDanger('Erreur upload', $fileGestion->getError());
                    goto out;
                }
                
                $data['image'] = $fileGestion->getFilename();
            }

            $form->verif($data);

            if (false === $form->isValid()) {
                goto out;
            }

            if (isset($data['on_homepage'])) {
                $data['on_homepage'] = 1;
            } else {
                $data['on_homepage'] = 0;
            }

            $data['slug'] = Helper::slugify($data['title']);
            $data['user'] = $this->app->user()->getId();
            if (true !== $this->manager->add('actualite', $data)) {
                $this->notifications->default('500', 'Erreur insertion', $this->manager->getError(), 'danger', $this->isDev());
            }

            $this->notifications->addSuccess('Actualité créée', 'L\'actualité a été créée avec succès.');

            return $this->response->referer();
        }

        out:

        return $this->render([
            'form' => $form
        ]);
    }

    public function update()
    {
        $actualiteId = $this->app->routeur()->getBag('id');

        $actualite = $this->manager->findOneBy('actualite', ['WHERE' => 'id = ' . $actualiteId]);

        $form = new ActualiteForm();

        if ($this->request->hasPost()) {
            $data = $this->request->getAllPost();

            $fileGestion = new FileManagement();
            $image = $_FILES['image'] ?? null;
            
            if (null !== $image && $image['error'] !== 4) {
                if (false === $fileGestion->uploadFile($_FILES['image'], $_FILES['image']['name'], 'img', 'actualite')) {
                    $this->notifications->addDanger('Erreur upload', $fileGestion->getError());
                    goto out;
                }
                
                $data['image'] = $fileGestion->getFilename();
            }

            $form->verif($data);

            if (false === $form->isValid()) {
                goto out;
            }
            if (isset($data['on_homepage'])) {
                $data['on_homepage'] = 1;
            } else {
                $data['on_homepage'] = 0;
            }
            if (empty($data['event_date'])) {
                $data['event_date'] = NULL;
            }
            $data['user'] = $this->app->user()->getId();
            $data['id'] = $actualiteId;
            $data['slug'] = Helper::slugify($data['title']);
            if (true !== $this->manager->update('actualite', $data)) {
                $this->notifications->default('500', 'Erreur insertion', $this->manager->getError(), 'danger', $this->isDev());
            }

            $this->notifications->addInfo('Actualité mise à jour', 'L\'actualité a bien été mise à jour.');

            return $this->response->referer();
        }

        out:

        return $this->render([
            'actualite' => $actualite,
            'form' => $form
        ]);
    }

    public function archive()
    {
        $actualiteFlags = [
            'WHERE' => [
                'table' => 'actualite',
                'tag' => 'archive',
                'value' => '1'
            ]
        ];

        $actualites = $this->manager->fetchAll('actualite', $actualiteFlags);

        return $this->render([
            'actualites' => $actualites
        ]);
    }

    public function archiverActualite()
    {
        $id = $this->app->routeur()->getBag('id');

        $data = [
            'id' => $id,
            'archive' => 1
        ];

        $this->manager->update('actualite', $data);
        $this->notifications->addSuccess('Mise à jour effectuée', "L'actualité a été archivée.");

        return $this->response->referer();
    }

    public function desarchiverActualite()
    {
        $id = $this->app->routeur()->getBag('id');

        $data = [
            'id' => $id,
            'archive' => 0
        ];

        $this->manager->update('actualite', $data);
        $this->notifications->addSuccess('Mise à jour effectuée', "L'actualité a été désarchivée.");

        return $this->response->referer();
    }

    public function updateHomepage()
    {
        $id = $this->app->routeur()->getBag('id');
        $value = $this->app->routeur()->getBag('value');

        $data = [
            'id' => $id,
            'on_homepage' => $value
        ];

        if (false === $this->manager->update('actualite', $data)) {
            return false;
        }

        $words = 'retirée de la';

        if ($value == 1) {
            $words = 'ajoutée à la ';
        }

        $this->notifications->addSuccess('Mise à jour réussie', "L'actualité a bien été $words page d'accueil.");

        return true;
    }

    public function imageLink()
    {
        $id = $this->app->routeur()->getBag('id');

        $data = [
            'id' => $id,
            'image' => null,
            'alt' => null
        ];

        $this->manager->update('actualite', $data);
    
        return true;
    }

    public function remove()
    {
        $actualiteId = $this->app->routeur()->getBag('id');

        $this->manager->remove('actualite', 'id', $actualiteId);

        $this->notifications->addDanger('Actualité supprimée', 'L\'actualité a correctement été supprimée.');

        return $this->response->referer();
    }
}
