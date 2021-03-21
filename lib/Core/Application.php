<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Core;

use Service\Response;
use Module\Notifications;

class Application extends Core
{
    public function __construct()
    {
        parent::__construct();
    }

    public function run()
    {
        $notification = Notifications::getInstance();
        $response = new Response();
        if (false === $this->routeur()->run()) {
            if (true === $this->config()->isDev()) {
                $notification->addDanger('Erreur fatale', $this->routeur()->getError());
            } else {
                $notification->addDanger('', 'La page que vous avez demandée n\'existe pas, ou plus.');
            }

            return $response->redirectTo('/');
        }

        if ($this->routeur()->getRoute()->getInterface() === 'backend') {
            $this->AdminAccess($response);
        }

        $page = new Page();
        $page->generate($this);
    }

    private function AdminAccess()
    {
        $response = new Response();
        if (!$this->authentification()->isAuthentificated()) {
            return $response->connect();
        }

        if (!($this->authentification()->hasRole('ROLE_SUPER_ADMIN') || $this->authentification()->hasRole('ROLE_ADMIN') || $this->authentification()->hasRole('ROLE_MODERATEUR'))) {
            return $response->disconnect();
        }
    }
}
