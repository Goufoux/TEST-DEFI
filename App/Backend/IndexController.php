<?php

namespace App\Backend;

use Core\AbstractController;
use Entity\Contact;
use Service\FileManagement;

class IndexController extends AbstractController
{
    public function index()
    {
        $contenus = $this->manager->fetchAll('contenu');

        $actualites = $this->manager->fetchAll('actualite');

        $produits = $this->manager->fetchAll('product');

        $contactsFlags = [
            'LEFT JOIN' => [
                'table' => 'contactDevis',
                'sndTable' => 'contact',
                'firstTag' => 'id',
                'sndTag' => 'contactDevis' 
            ]
        ];

        $contacts = $this->manager->fetchAll('contact', $contactsFlags);

        $unChecked = 0;

        /** @var Contact $contact */
        foreach ($contacts as $contact) {
            if (false == $contact->getChecked()) {
                $unChecked++;
            }
        }

        return $this->render([
            'title' => 'Genarkys - Backend',
            'contenus' => $contenus,
            'actualites' => $actualites,
            'produits' => $produits,
            'contacts' => $contacts,
            'unchecked' => $unChecked
        ]);
    }

    public function restore()
    {
        $dir = __DIR__ . '/../../upload/img/';

        $scanDir = scandir($dir);

        
        foreach ($scanDir as $key => $path) {
            if (!is_dir($dir . $path) || $path === '.' || $path === '..' || in_array($path, ['actualite', 'contenu', 'r-product', 'produits', 'rubrique'])) {
                unset($scanDir[$key]);
            }
        }
        

        
        $imgs = [];

        foreach ($scanDir as $key => $path) {
            $scanRubDir = scandir($dir.$path.'/');
            
            foreach ($scanRubDir as $imgPath) {
                if ($imgPath === '.' || $imgPath === '..') {
                    continue;
                }
                $imgs[$path][] = $imgPath;
            }

        }

        $fm = new FileManagement();

        var_dump($imgs);
        
        foreach ($imgs as $rubrique => $array) {
            foreach ($array as $key => $imgPath) {
                // $fm->uploadFile($imgPath, )
                // $extension = $fm->getExtension($imgPath);
                // var_dump($extension, $fm->getError());
                // var_dump($fm->saveReverseImage($imgPath, 'jpg', $dir.$rubrique.'/'));
                // exit;
                $fm->saveReverseImage($imgPath, 'jpg', $dir.$rubrique.'/');
                // die($extension);

            }
        }

        exit;
    }
}
