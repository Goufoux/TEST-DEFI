<?php

namespace App\Backend;

use Core\AbstractController;
use Form\ImageForm;
use Service\FileManagement;

class UploaderController extends AbstractController
{
    public function index()
    {
        $images = $this->manager->fetchAll('image');

        return $this->render([
            'images' => $images
        ]);
    }

    public function new()
    {
        $form = new ImageForm();

        if ($this->request->hasPost()) {
            $data = $this->request->getAllPost();

            $fileGestion = new FileManagement();    
            $image = ($_FILES['file']['error'] == 4) ? null : $_FILES['file'];
            if (null !== $image) {
                if (false === $fileGestion->uploadFile($_FILES['file'], uniqid(), 'img')) {
                    $this->notifications->addDanger('Erreur upload', $fileGestion->getError());
                    goto out;
                }
            }

            $data['path'] = $fileGestion->getFilename();
            $data['size'] = $fileGestion->getSize();
            $form->verif($data);

            if ($form->isValid()) {
                if (false === $this->manager->add('image', $data)) {
                    $this->notifications->default('500', 'Erreur', $this->manager->getError(), 'danger', $this->isDev());
                
                    goto out;
                }
                $this->notifications->addSuccess('Upload effectué.', 'Le fichier ' . $data['name'] . ' a bien été téléchargé.');
                
                return $this->response->referer();
            }
        }

        out:

        return $this->render([
            'form' => $form
        ]);
    }

    public function remove()
    {
        $imageId = $this->app->routeur()->getBag('id');

        $this->manager->remove('image', 'id', $imageId);

        $this->notifications->addSuccess('Suppression effectuée', 'L\'image a été supprimée et retirée du serveur.');

        return $this->response->referer();
    }
}
