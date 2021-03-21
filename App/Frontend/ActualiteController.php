<?php

namespace App\Frontend;

use Core\AbstractController;
use DateTime;
use Entity\Actualite;

class ActualiteController extends AbstractController
{
    public function index()
    {
        $actualites = $this->manager->fetchAll('actualite', 
            ['WHERE' => [
                'table' => 'actualite', 
                'tag' => 'archive', 
                'value' => 0
            ], 
            'ORDER BY' => [
                'table' => 'actualite', 
                'tag' => 'event_date', 
                'type' => 'DESC'
            ]
        ]);

        $today = new DateTime();

        /** @var Actualite $actualite */
        foreach ($actualites as $key => $actualite) {
            if ($actualite->getArchive()) {
                unset($actualites[$key]);
                continue;
            }
            $eventDate = new DateTime($actualite->getEventDate());
            if ($eventDate > $today) {
                unset($actualites[$key]);
                continue;
            }

        }

        return $this->render([
            'actualites' => $actualites,
            'title' => 'LCI - Nos actualités'
        ]);
    }

    public function view()
    {
        $id = $this->app->routeur()->getBag('id');
        
        if (null === $id) {
            $this->notifications->default('500', 'Identifiant non trouvé', '$id est manquant', 'danger', $this->isDev());
            $this->response->referer();
        }

        $actualite = $this->manager->findOneBy('actualite', ['WHERE' => 'id = ' . $id]);

        if (null === $actualite || false === $actualite) {
            $this->notifications->default('500', 'Contenu non trouvé', 'La page n\'existe pas', 'danger', $this->isDev());
            $this->response->referer();            
        }

        return $this->render([
            'actualite' => $actualite,
            'title' => "LCI - {$actualite->getMetaTitle()}"
        ]);
    }
}
