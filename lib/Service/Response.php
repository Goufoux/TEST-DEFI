<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Service;

class Response
{
    /**
     * connect()
     *
     * Redirection vers la page de connexion
     * @return void
     */
    public function connect()
    {
        header('Location: /connect/');
        return;
    }

    /**
     * redirectTo(string $location)
     *
     * Redirection vers $location
     * @return void|false
     */
    public function redirectTo(string $location = ''): bool
    {
        if (empty($location)) {
            return false;
        }

        header('Location: '.$location);
        return true;
    }

    public function disconnect()
    {
        header('Location: /logout');
    }

    public function referer($args = '')
    {
        $request = new Request;
        $referer = $request->getHttpReferer();
        if ($referer == null) {
            header('Location: /');
            exit;
        }
        header('Location: '.$referer.$args);
        exit;
    }

    public function errors(string $message = '', int $code = 0)
    {
        if ($code == 500) {
            header('Location: /error.html');
        }
        header('Location: /error');
    }
}
