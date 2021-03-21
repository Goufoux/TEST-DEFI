<?php

namespace App\Backend;

use Core\AbstractController;
use Entity\Contact;

class FormulaireController extends AbstractController
{
    public function index()
    {
        $contactsFlags = [
            'LEFT JOIN' => [
                'table' => 'contactDevis',
                'sndTable' => 'contact',
                'firstTag' => 'id',
                'sndTag' => 'contactDevis' 
            ]
        ];

        $contacts = $this->manager->fetchAll('contact', $contactsFlags);
        

        return $this->render([
            'contacts' => $contacts
        ]);
    }

    public function view()
    {
        $contactsFlags = [
            'LEFT JOIN' => [
                'table' => 'contactDevis',
                'sndTable' => 'contact',
                'firstTag' => 'id',
                'sndTag' => 'contactDevis' 
            ]
        ];

        $contactId = $this->app->routeur()->getBag('id'); 

        $contact = $this->manager->findOneBy('contact', ['WHERE' => "id = $contactId"], $contactsFlags);

        $data = [
            'id' => $contactId,
            'checked' => 1
        ];

        $this->manager->update('contact', $data);

        return $this->render([
            'contact' => $contact
        ]);
    }

    public function remove()
    {
        $contactId = $this->app->routeur()->getBag('id'); 

        /** @var Contact $contact */
        $contact = $this->manager->findOneBy('contact', ['WHERE' => "id = $contactId"]);

        if ($contact->getContactDevis() !== null) {
            $this->manager->remove('contactDevis', 'id', $contact->getContactDevis());
        }

        $this->manager->remove('contact', 'id', $contactId);

        $this->notifications->addDanger('Suppression effectuée', 'Le formulaire de contact a correctement été supprimé.');

        return $this->response->referer();
    }
}
