<?php 

namespace App\Backend;

use Core\AbstractController;
use Entity\ProductRubrique;
use Entity\Rubrique;
use Form\RubriqueForm;
use Service\Helper;

class RubriqueController extends AbstractController
{
    public function index()
    {
        $flag = [
            'ORDER BY' => [
                'table' => 'rubrique',
                'tag' => 'name',
                'type' => 'ASC' 
            ]
        ];

        $rubriques = $this->manager->fetchAll('rubrique', $flag);

        return $this->render([
            'rubriques' => $rubriques
        ]);
    }

    public function new()
    {
        $form = new RubriqueForm();

        if ($this->request->hasPost()) {
            $data = $this->request->getAllPost();

            $form->verif($data);

            if (false === $form->isValid()) {
                goto out;
            }

            $data['user'] = $this->app->user()->getId();
            $data['slug'] = Helper::slugify($data['name']);

            if (false === $this->manager->add('rubrique', $data)) {
                $this->notifications->default('500', 'Une erreur est survenue.', $this->manager->getError(), 'danger', $this->isDev());

                goto out;
            }

            $this->notifications->addSuccess('Rubrique ajouté', 'La rubrique <strong>'.$data['name'].'</strong> a été ajoutée.');

            return $this->response->referer();
        }

        out:

        return $this->render([
            'form' => $form
        ]);
    }

    public function update()
    {
        $slug = $this->app->routeur()->getBag('slug');

        /** @var Rubrique $rubrique */
        $rubrique = $this->manager->findOneBy('rubrique', ['WHERE' => "slug = '$slug'"]);

        $form = new RubriqueForm();

        if ($this->request->hasPost()) {
            $data = $this->request->getAllPost();

            $form->verif($data);

            if (false === $form->isValid()) {
                goto out;
            }
            $data['id'] = $rubrique->getId();
            $data['user'] = $this->app->user()->getId();
            $data['slug'] = Helper::slugify($data['name']);

            if (false === $this->manager->update('rubrique', $data)) {
                $this->notifications->default('500', 'Une erreur est survenue.', $this->manager->getError(), 'danger', $this->isDev());

                goto out;
            }

            $this->notifications->addSuccess('Rubrique mise à jour', 'La rubrique <strong>'.$data['name'].'</strong> a été mise à jour.');

            return $this->response->referer();
        }

        out:

        return $this->render([
            'rubrique' => $rubrique,
            'form' => $form
        ]);
    }

    public function remove()
    {
        $rubriqueId = $this->app->routeur()->getBag('id');

        $this->manager->remove('rubrique', 'id', $rubriqueId);

        $this->notifications->addSuccess('Suppression effectuée', 'La rubrique a correctement été supprimé');

        return $this->response->referer();
    }
}
