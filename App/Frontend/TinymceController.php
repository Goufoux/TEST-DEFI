<?php

namespace App\Frontend;

use Core\AbstractController;
use EdSDK\FlmngrServer\FlmngrServer;
use Service\FileManagement;

class TinymceController extends AbstractController
{
    public function upload()
    {
        $response = ['erreur' => 'Impossible de telecharger l\'image'];
        if (empty($_FILES['file'])) {
            return json_encode($response);
        }

        $refererUrl = $this->request->getHttpReferer();

        if (null === $refererUrl) {
            return json_encode(['Erreur' => 'Impossible de telecharger l\'image']);
        }

        $source = false;

        if (preg_match("/product/", $refererUrl)) {
            $source = 'product';
        } elseif (preg_match("/contenu/", $refererUrl)) {
            $source = 'contenu';
        } elseif (preg_match("/rubrique/", $refererUrl)) {
            $source = 'rubrique';
        } elseif (preg_match("/actualite/", $refererUrl)) {
            $source = 'actualite';
        } else {
            return json_encode(['Erreur' => 'Impossible de telecharger l\'image']);
        }

        $fileManagement = new FileManagement();

        // return json_encode(['name' => $_FILES['file']['name']]);

        if (false === $fileManagement->uploadFile($_FILES['file'], $_FILES['file']['name'], 'img', $source)) {
            return json_encode(['Erreur' => $fileManagement->getError()]);
        }
        

        $location = FileManagement::REL_PATH.'/img/'.$fileManagement->getFilename();

        return json_encode(['location' => $location]);
    }

    

    public function galleries()
    {
        $productId = $this->app->routeur()->getBag('id');
        
        if (null === $productId) {
            return false;
        }

        $product = $this->manager->findOneBy('product', ['WHERE' => "id = $productId"]);

        if (false === $product) {
            return false;
        }

        $productGalleriesFlags = [
            'LEFT JOIN' => [
                'table' => 'image',
                'sndTable' => 'productImage',
                'firstTag' => 'id',
                'sndTag' => 'image'
            ]
        ];

        $productGalleries = $this->manager->findBy('productImage', ['WHERE' => "product = {$product->getId()}"], $productGalleriesFlags);

        return $this->render(['product' => $product, 'galleries' => $productGalleries]);
    }
}
