<?php

namespace App\Backend;

use Core\AbstractController;

class ConnexionController extends AbstractController
{
    public function index()
    {
        if (false === $this->app->auth()->hasRole('ROLE_SUPER_ADMIN')) {
            $this->response->referer();
        }
        
        $flags = [
            'INNER JOIN' => [
                'table' => 'user',
                'sndTable' => 'connexion',
                'firstTag' => 'id',
                'sndTag' => 'user'
            ],
            'ORDER BY' => [
                'table' => 'connexion',
                'tag' => 'created_at',
                'type' => 'DESC'
            ]
        ];

        $connexions = $this->manager->fetchAll('connexion', $flags);

        return $this->render([
            'connexions' => $connexions
        ]);
    }

    public function truncate()
    {
        $this->manager->truncate('connexion');

        $this->notifications->addSuccess('Table vidée', 'La table <strong>Connexion</strong> a été vidée.');

        return $this->response->referer();
    }
}
