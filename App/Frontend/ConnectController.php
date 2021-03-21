<?php

namespace App\Frontend;

use Core\AbstractController;
use DateTime;
use Form\ConnectForm;
use Manager\UserManager;

class ConnectController extends AbstractController
{
    public function index()
    {
        $form = new ConnectForm();

        if ($this->request->hasPost()) {
            $data = $this->request->getAllPost();
            $form->verif($data);
            if (true === $form->isValid()) {
                if (false === $user = $this->connectUser($data)) {
                    $form->addErrors('email', 'Identifiants incorrects');
                    
                    goto out;
                }
                
                $_SESSION['user'] = $user;
                
                $data = [
                    'user' => $user->getId(),
                    'ip' => $this->request->getRemoteAddr()
                ];
                
                $this->manager->add('connexion', $data);

                $today = new DateTime();

                if ($today->format('H') > 17 && $today->format('H') < 4) {
                    $msg = 'Bonsoir';
                } else {
                    $msg = 'Bonjour';
                }

                $this->notifications->addSuccess('Connexion réussie', "$msg {$user->getFirstName()}");
                
                return $this->response->redirectTo('/');
            }
        }

        out:

        return $this->render([
            'form' => $form
        ]);
    }

    private function connectUser(array $data)
    {
        /** @var UserManager $userManager */
        $userManager = $this->manager->getManagerOf('User');

        // var_dump($userManager); exit;
        
        return $userManager->connect($data['email'], $data['password']);
    }
}
