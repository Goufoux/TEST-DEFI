<?php

namespace Manager;

use Core\Managers;
use PDOException;

class UserManager extends Managers
{
    public function connect($email, $password)
    {
        $user = $this->findOneBy('user', ['WHERE' => "email = '$email'"]);

        try {
            if (false === $user) {
                throw new PDOException('Identifiants incorrects.');
            }

            if (false === password_verify($password, $user->getPassword())) {
                throw new PDOException('Identifiants incorrects.');                
            }
            
            return $user;

        } catch(PDOException $e) {
            $this->setError($e->getMessage());

            return false;
        }
    }
}