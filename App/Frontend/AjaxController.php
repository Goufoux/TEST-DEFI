<?php

namespace App\Frontend;

use Core\AbstractController;
use Module\Notifications;

class AjaxController extends AbstractController
{
    public function notifications()
    {
        $notifications = Notifications::getInstance();
        $array = $notifications->getMessages();
        $final = [];
        foreach ($array as $type => $messages) {
            $final[] = ['type' => $type, 'messages' => $messages];
        }
        $notifications->clearMessages();
        return json_encode($final);
    }
}
