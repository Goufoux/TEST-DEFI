<?php

namespace App\Frontend;

use Core\AbstractController;
use Entity\Product;

class ProductController extends AbstractController
{
    public function view()
    {
        $id = $this->app->routeur()->getBag('id');
        
        if (null === $id) {
            $this->notifications->default('500', 'Identifiant non trouvé', '$id est manquant', 'danger', $this->isDev());
            $this->response->referer();
        }

        /** @var Product $product */
        $product = $this->manager->findOneBy('product', ['WHERE' => 'id = ' . $id]);

        if (null === $product || false === $product) {
            $this->notifications->default('500', 'Contenu non trouvé', 'La page n\'existe pas', 'danger', $this->isDev());
            $this->response->referer();            
        }

        $productRubriquesFlag = [
            'LEFT JOIN' => [
                'table' => 'rubrique',
                'sndTable' => 'productRubrique',
                'firstTag' => 'id',
                'sndTag' => 'rubrique' 
            ]
        ];

        $productRubriques = $this->manager->findBy('productRubrique', ['WHERE' => "product = {$product->getId()}"], $productRubriquesFlag);
        $product->setRubriques($productRubriques);
        
        if (null !== $productRubriques && !empty($productRubriques)) {
            $productGalleriesFlags = [
                'LEFT JOIN' => [
                    'table' => 'image',
                    'sndTable' => 'productImage',
                    'firstTag' => 'id',
                    'sndTag' => 'image'
                ]
            ];
    
            $productGalleries = $this->manager->findBy('productImage', ['WHERE' => "product = {$product->getId()}"], $productGalleriesFlags);
    
            $similarRubrique = $productRubriques[rand(0, count($productRubriques)-1)];
    
            $similarProductFlag = [
                'LEFT JOIN' => [
                    'table' => 'product',
                    'sndTable' => 'productRubrique',
                    'firstTag' => 'id',
                    'sndTag' => 'product' 
                ]
            ];
    
            $similarProducts = $this->manager->findBy('productRubrique', ['WHERE' => "rubrique = {$similarRubrique->getRubrique()->getId()} AND productRubrique_product != {$product->getId()}"], $similarProductFlag);
        
        } else {
            $productGalleries = [];
            $similarProducts = null;

        }


        return $this->render([
            'product' => $product,
            'title' => 'LCI - ' . $product->getMetaTitle(),
            'galleries' => $productGalleries,
            'similarProducts' => $similarProducts
        ]);
    }
}
