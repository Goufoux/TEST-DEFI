<?php

namespace App\Frontend;

use Core\AbstractController;
use Entity\Contenu;

class ContenuController extends AbstractController
{
    public function contenu()
    {
        $id = $this->app->routeur()->getBag('id');
        
        if (null === $id) {
            $this->notifications->default('500', 'Identifiant non trouvé', '$id est manquant', 'danger', $this->isDev());
            $this->response->referer();
        }

        /** @var Contenu $contenu */
        $contenu = $this->manager->findOneBy('contenu', ['WHERE' => 'id = ' . $id]);

        if (null === $contenu || false === $contenu) {
            $this->notifications->default('500', 'Contenu non trouvé', 'La page n\'existe pas', 'danger', $this->isDev());
            $this->response->referer();            
        }

        $data = [
            'view' => $contenu->getView() + 1,
            'id' => $contenu->getId()
        ];

        $this->manager->update('contenu', $data);

        return $this->render([
            'contenu' => $contenu,
            'title' => "LCI - {$contenu->getTitle()}"
        ]);
    }
}
