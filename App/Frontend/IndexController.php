<?php

namespace App\Frontend;

use Core\AbstractController;
use DateTime;
use Entity\Actualite;
use Form\ContactDevisForm;
use Form\ContactForm;
use Module\Mail;
use Service\FileManagement;

class IndexController extends AbstractController
{
    const PRODUCT_VIEW = 4;

    public function index()
    {
        $Allproducts = $this->manager->fetchAll('product');

        $maxProduits = count($Allproducts)-1;
        
        $produits = [];
        $productsAlreadyAdded = [];

        for ($i = 0; $i < self::PRODUCT_VIEW; $i++) {
            restart:
            $temp = $Allproducts[rand(0, $maxProduits)];
            if (in_array($temp->getId(), $productsAlreadyAdded)) {
                goto restart;
            }
            $produits[] = $temp;
            $productsAlreadyAdded[] = $temp->getId();
        }

        $actualites = $this->manager->fetchAll('actualite', [
            'WHERE' => [
                'table' => 'actualite', 
                'tag' => 'on_homepage', 
                'value' => '1'
            ], 
            'ORDER BY' => [
                'table' => 'actualite', 
                'tag' => 'event_date', 
                'type' => 'DESC'
            ]
        ]);

        $data = [];
        $keys = [];
        $compteur = 0;
        $key = 0;
        $today = new DateTime();

        $nbActu = 3;
        /** @var Actualite $actualite */
        foreach ($actualites as $k => $actualite) {
            if ($compteur >= 3) {
                continue;
            }
            if ($actualite->getArchive() == true) {
                continue;
            }
            if (null !== $actualite->getEventDate()) {
                $eventDate = new DateTime($actualite->getEventDate());
                if ($eventDate > $today) {
                    continue;
                }
            }
            $data[$k] = $actualite;
            $compteur++;
        }
        
        
        return $this->render([
            'produits' => $produits,
            'actualites' => $data,
            'diapoKey' => $keys,
            'nbActu' => $nbActu
        ]);
    }

    public function logout()
    {
        unset($_SESSION);
        session_destroy();

        return $this->response->redirectTo('/');
    }

    public function dimension()
    {
        $data = $this->request->getAllData();

        if (empty($data)) {
            return false;
        }

        $height = $data['height'] ?? null;
        $width = $data['width'] ?? null;

        if (null === $height || null === $width) {
            return false;
        }

        $_SESSION['height'] = $height;
        $_SESSION['width'] = $width;

        echo 'ok';

        return;

    }

    public function contact()
    {
        $form = new ContactForm();

        if ($this->request->hasPost()) {
            $data = $this->request->getAllPost();

            if (isset($data['devis_form'])) {
                $form = new ContactDevisForm();
                unset($data['devis_form']);
                $form->verif($data);

                $fileGestion = new FileManagement();
                $devisData = [];
                if (!empty($_FILES['file']) && $_FILES['file']['error'] !== 4) {

                    if (false === $fileGestion->uploadFile($_FILES['file'], uniqid(), 'all', 'formulaire')) {
                        $this->notifications->addDanger('Erreur d\'upload', $fileGestion->getError);

                        goto out;
                    }

                    $devisData['path'] = $fileGestion->getFilename();
                }


                if (false === $form->isValid()) {
                    goto out;
                }

                $devisDataField = ['product_weight', 'type_emballage', 'dim_longueur', 'dim_largeur', 'dim_hauteur', 'finition', 'quantity', 'annual_need'];

                foreach ($data as $key => $value) {
                    if (in_array($key, $devisDataField)) {
                        $devisData[$key] = $value;
                        unset($data[$key]);
                    }
                }

                if (false === $this->manager->add('contactDevis', $devisData)) {
                    $this->notifications->default('500', 'Une erreur est survenue', $this->manager->getError(), 'danger', $this->isDev());

                    goto out;
                }

                $contactDevisId = $this->manager->getLastInsertId();
                $data['contactDevis'] = $contactDevisId;

                if (false === $this->manager->add('contact', $data)) {
                    $this->notifications->default('500', 'Une erreur est survenue', $this->manager->getError(), 'danger', $this->isDev());

                    goto out;
                }

                $mailService = new Mail();
                $data['devis_form'] = true;
                foreach ($devisData as $k => $v) {
                    $data[$k] = $v;
                }
                if (false === $mailService->sendEmail('Demande de devis', $data)) {
                    $this->notifications->addWarning('échec de l\'envoi', 'Votre message n\'a pas été transmis.');

                    goto out;
                }

                $this->notifications->addSuccess('Formulaire validé', 'Votre message a été transmis.');

                return $this->response->redirectTo('/contact');

            } else {
                $form->verif($data);

                if (false === $form->isValid()) {
                    goto out;
                }

                if (false === $this->manager->add('contact', $data)) {
                    $this->notifications->default('500', 'Une erreur est survenue', 'danger', $this->manager->getError(), $this->isDev());

                    goto out;
                }
                
                $mailService = new Mail();

                if (false === $mailService->sendEmail('Demande de contact', $data)) {
                    $this->notifications->addWarning('échec de l\'envoi', 'Votre message n\'a pas été transmis.');

                    goto out;
                }

                $this->notifications->addSuccess('Formulaire validé', 'Votre message a été transmis.');

                return $this->response->redirectTo('/contact');
            }
        }

        out:

        return $this->render([
            'form' => $form
        ]);
    }
}
