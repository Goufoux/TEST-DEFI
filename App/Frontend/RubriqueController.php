<?php

namespace App\Frontend;

use Core\AbstractController;
use Entity\Product;
use Entity\ProductRubrique;
use Entity\Rubrique;

class RubriqueController extends AbstractController
{
    public function view()
    {
        $slug = $this->app->routeur()->getBag('slug');
        
        if (null === $slug) {
            $this->notifications->default('500', 'Identifiant non trouvé', '$slug est manquant', 'danger', $this->isDev());
            $this->response->referer();
        }

        /** @var Rubrique $rubrique */
        $rubrique = $this->manager->findOneBy('rubrique', ['WHERE' => "slug = '$slug'"]);

        if (null === $rubrique || false === $rubrique) {
            $this->notifications->default('500', 'Contenu non trouvé', 'La page n\'existe pas', 'danger', $this->isDev());
            $this->response->referer();            
        }

        $cnx = $this->app->getDatabase()->bdd();

        $req = $cnx->query("SELECT productRubrique.*, product.* 
        FROM productRubrique 
        LEFT JOIN product ON product.product_id = productRubrique.productRubrique_product 
        WHERE productRubrique_rubrique = {$rubrique->getId()} ORDER BY product.product_created_at DESC");
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\\ProductRubrique');        
        $req->execute();

        $products = $req->fetchAll();

        foreach ($products as $key => $product) {
            $products[$key] = new ProductRubrique($product, true);
        }

        // Aléatoire
        $finalProducts = [];

        if (4 < count($products)) {
            $alreadyUse = [];
            for ($i = 0; $i < 4; $i++) {
                retry:
                $preKey = rand(0, count($products)-1);

                if (true === in_array($preKey, $alreadyUse)) {
                    goto retry;
                }

                $finalProducts[] = $products[$preKey];
                $alreadyUse[] = $preKey;
            }
        } else {
            $finalProducts = $products;
        }


        return $this->render([
            'rubrique' => $rubrique,
            'title' => 'LCI - ' . $rubrique->getMetaTitle(),
            'products' => $finalProducts
        ]);
    }
}
