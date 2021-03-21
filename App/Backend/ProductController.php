<?php 

namespace App\Backend;

use Core\AbstractController;
use Entity\Image;
use Entity\Product;
use Entity\ProductRubrique;
use Form\ProductForm;
use Service\FileManagement;
use Service\Helper;

class ProductController extends AbstractController
{
    const PRODUCT_LIMIT = 10;

    public function index()
    {
        $page = $this->get('page', 'GET', false, false);

        if (null === $page) {
            $page = 1;
        }

        $offset = ($page - 1) * self::PRODUCT_LIMIT;

        $flags = [
            'ORDER BY' => [
                'table' => 'product',
                'tag' => 'created_at',
                'type' => 'DESC'
            ],
            'LIMIT' => [
                'value' => self::PRODUCT_LIMIT
            ],
            'OFFSET' => [
                'value' => $offset
            ]
        ];
        $productsNb = $this->manager->fetchAll('product');
        $productsNb = count($productsNb);
        $products = $this->manager->fetchAll('product', $flags);

        /** @var Product $product */
        foreach ($products as $product) {
            $rubriques = $this->manager->findBy('productRubrique', ['WHERE' => "product = {$product->getId()}"]);

            /** @var ProductRubrique $item */
            foreach ($rubriques as $item) {
                $rubrique = $this->manager->findOneBy('rubrique', ['WHERE' => "id = {$item->getRubrique()}"]);
                $item->setRubrique($rubrique);
            }

            $product->setRubriques($rubriques);
        }

        return $this->render([
            'products' => $products,
            'nb' => $productsNb,
            'limit' => self::PRODUCT_LIMIT
        ]);
    }

    public function search()
    {
        $name = $this->get('name', 'POST', false, false);

        $cnx = $this->app->getDatabase()->bdd();

        $req = $cnx->query("SELECT product_id, product_name, product_image, product_slug FROM product WHERE product_name LIKE '%$name%'");
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\\Product');        
        $req->execute();

        $products = $req->fetchAll();

        if (empty($products)) {
            return false;
        }

        return json_encode($products);
    }

    public function new()
    {
        $rubriques = $this->manager->fetchAll('rubrique');

        $form = new ProductForm();

        if ($this->request->hasPost()) {
            $data = $this->request->getAllPost();

            $fileGestion = new FileManagement();

            $galleriesKey = [];
            /* galleries */
            if (!empty($_FILES)) {
                $galleries = [];
                
                foreach ($_FILES as $key => $value) {
                    if (!preg_match('/galleries/', $key)) {
                        continue;    
                    }
                    $tempName = explode('_', $key);
                    if (!isset($tempName[1])) {
                        $this->notifications->addWarning('Erreur upload', 'Impossible d\'importer une image de la gallerie, clé manquante.');
                        continue;
                    }
                    if (!isset($data['alt_galleries_'.$tempName[1]])) {
                        $this->notifications->addWarning('Erreur upload', 'Une image n\'a pas été importé car la balise alt n\'était pas renseignée. {alt_galleries_'.$tempName[1].'}');
                        continue;
                    }

                    if (false === $fileGestion->uploadFile($_FILES[$key], $_FILES[$key]['name'], 'img', 'product', true)) {
                        $this->notifications->addDanger('Erreur upload', $fileGestion->getError());
                        continue;
                    }
                    $galleries[] = [
                        'alt' => $data['alt_galleries_'.$tempName[1]],
                        'image' => $fileGestion->getFilename(),
                        'size' => $fileGestion->getSize()
                    ];
                    unset($data['alt_galleries_'.$tempName[1]]);
                }

                foreach ($galleries as $gallerie) {
                    $dataImage = [
                        'name' => $gallerie['image'],
                        'path' => $gallerie['image'],
                        'size' => $gallerie['size'],
                        'alt' => $gallerie['alt']
                    ];

                    if (false === $this->manager->add('image', $dataImage)) {
                        $this->notifications->addDanger('Erreur insertion', $this->manager->getError());
                        continue;
                    }

                    $galleriesKey[] = $this->manager->getLastInsertId();

                }
            }

            // var_dump($_POST, $_FILES); exit;

            $image = $_FILES['image'] ?? null;
            $drubriques = $data['rubriques'] ?? null;
            
            if (null !== $drubriques) {
                unset($data['rubriques']);
            }

            if (null !== $image) {
                if (false === $fileGestion->uploadFile($_FILES['image'], $_FILES['image']['name'], 'img', 'product', true)) {
                    $this->notifications->addDanger('Erreur upload', $fileGestion->getError());
                    goto out;
                }

                $data['image'] = $fileGestion->getFilename();
            }

            $form->verif($data);

            if (false === $form->isValid()) {
                goto out;
            }

            $data['slug'] = Helper::slugify($data['name']);
            $data['user'] = $this->app->user()->getId();
            if (true !== $this->manager->add('product', $data)) {
                $this->notifications->default('500', 'Erreur insertion', $this->manager->getError(), 'danger', $this->isDev());
            }

            $productId = $this->manager->getLastInsertId();

            foreach ($galleriesKey as $key) {
                $pgdata = [
                    'product' => $productId,
                    'image' => $key
                ];
                if (false === $this->manager->add('productImage', $pgdata)) {
                    $this->notifications->addDanger('Erreur insertion relation produit-image', $this->manager->getError());
                    continue;
                }
            }

            if (null !== $drubriques) {
                $this->addRubriques($drubriques, $productId);
            }


            $this->notifications->addSuccess('Produit ajouté', 'Le produit a été créé avec succès');

            return $this->response->referer();
        }

        out:

        return $this->render([
            'form' => $form,
            'rubriques' => $rubriques
        ]);
    }

    public function update()
    {
        $productId = $this->app->routeur()->getBag('id');

        $product = $this->manager->findOneBy('product', ['WHERE' => 'id = ' . $productId]);

        $rubriques = $this->manager->fetchAll('rubrique');

        $productRubriques = $this->manager->findBy('productRubrique', ['WHERE' => "product = $productId"]);
        $product->setRubriques($productRubriques);
        
        $productGalleriesFlags = [
            'LEFT JOIN' => [
                'table' => 'image',
                'sndTable' => 'productImage',
                'firstTag' => 'id',
                'sndTag' => 'image'
            ]
        ];

        $productGalleries = $this->manager->findBy('productImage', ['WHERE' => "product = {$product->getId()}"], $productGalleriesFlags);
        
        $form = new ProductForm();

        if ($this->request->hasPost()) {
            $data = $this->request->getAllPost();
            $fileGestion = new FileManagement();

            $galleriesKey = [];
            /* galleries */
            if (!empty($_FILES)) {
                $galleries = [];
                
                foreach ($_FILES as $key => $value) {
                    if (!preg_match('/galleries/', $key)) {
                        continue;    
                    }
                    $tempName = explode('_', $key);
                    if (!isset($tempName[1])) {
                        $this->notifications->addWarning('Erreur upload', 'Impossible d\'importer une image de la gallerie, clé manquante.');
                        continue;
                    }
                    if (!isset($data['alt_galleries_'.$tempName[1]])) {
                        $this->notifications->addWarning('Erreur upload', 'Une image n\'a pas été importé car la balise alt n\'était pas renseignée. {alt_galleries_'.$tempName[1].'}');
                        continue;
                    }

                    if (false === $fileGestion->uploadFile($_FILES[$key], $_FILES[$key]['name'], 'img', 'product', true)) {
                        $this->notifications->addDanger('Erreur upload', $fileGestion->getError());
                        continue;
                    }
                    $galleries[] = [
                        'alt' => $data['alt_galleries_'.$tempName[1]],
                        'image' => $fileGestion->getFilename(),
                        'size' => $fileGestion->getSize()
                    ];
                    unset($data['alt_galleries_'.$tempName[1]]);
                }

                foreach ($galleries as $gallerie) {
                    $dataImage = [
                        'name' => $gallerie['image'],
                        'path' => $gallerie['image'],
                        'size' => $gallerie['size'],
                        'alt' => $gallerie['alt']
                    ];

                    if (false === $this->manager->add('image', $dataImage)) {
                        $this->notifications->addDanger('Erreur insertion', $this->manager->getError());
                        continue;
                    }

                    $galleriesKey[] = $this->manager->getLastInsertId();

                }
            }

            $image = $_FILES['image'] ?? null;
            $this->manager->remove('productRubrique', 'product', $productId);        
            $drubriques = $data['rubriques'] ?? null;
            
            if (null !== $drubriques) {
                unset($data['rubriques']);
            }
            
            
            if (null !== $image && $image['error'] !== 4) {
                if (false === $fileGestion->uploadFile($_FILES['image'], $_FILES['image']['name'], 'img', 'product', true)) {
                    $this->notifications->addDanger('Erreur upload', $fileGestion->getError());
                    goto out;
                }
                
                $data['image'] = $fileGestion->getFilename();
            }

            $form->verif($data);

            if (false === $form->isValid()) {
                goto out;
            }

            $data['user'] = $this->app->user()->getId();
            $data['id'] = $productId;
            $data['slug'] = Helper::slugify($data['name']);
            if (true !== $this->manager->update('product', $data)) {
                $this->notifications->default('500', 'Erreur insertion', $this->manager->getError(), 'danger', $this->isDev());
            }

            if (null !== $drubriques) {
                $this->addRubriques($drubriques, $product->getId());
            }

            foreach ($galleriesKey as $key) {
                $pgdata = [
                    'product' => $productId,
                    'image' => $key
                ];
                if (false === $this->manager->add('productImage', $pgdata)) {
                    $this->notifications->addDanger('Erreur insertion relation produit-image', $this->manager->getError());
                    continue;
                }
            }

            $this->notifications->addSuccess('Produit ajouté', 'Le produit <strong>'. $data['name'] .'</strong> a été mis à jour');

            return $this->response->referer();

        }

        out:

        return $this->render([
            'product' => $product,
            'rubriques' => $rubriques,
            'form' => $form,
            'productRubriques' => $productRubriques,
            'galleries' => $productGalleries
        ]);
    }

    public function removeImage()
    {
        $id = $this->app->routeur()->getBag('id');
    
        $data = [
            'id' => $id,
            'image' => null,
            'alt' => null
        ];

        $this->manager->update('product', $data);

        return true;
    }

    public function imageLink()
    {
        $slug = $this->app->routeur()->getBag('slug');

        $productImageId = explode('-', $slug);
        $this->manager->remove('productImage', 'id', $productImageId[1]);

        return true;
    }

    public function addRubriques(array $rubriques, int $productId)
    {
        foreach ($rubriques as $rubriqueId) {
            $data = [
                'rubrique' => $rubriqueId,
                'product' => $productId
            ];

            if (false === $this->manager->add('productRubrique', $data)) {
                $this->notifications->default('500', 'Une erreur est survenue', $this->manager->getError(), 'danger', $this->isDev());
            }
        }
    }

    public function remove()
    {
        $productId = $this->app->routeur()->getBag('id');

        $this->manager->remove('productRubrique', 'product', $productId);
        $this->manager->remove('productImage', 'product', $productId);
        $this->manager->remove('product', 'id', $productId);

        $this->notifications->addSuccess('Produit supprimé', 'Le produit a correctement été supprimé.');

        return $this->response->referer();
    }

    public function mevGallery()
    {
        $galleryId = $this->app->routeur()->getBag('id');

        $gallery = $this->manager->findOneBy('productImage', ['WHERE' => "id = $galleryId"]);

        /** @var Product $product */
        $product = $this->manager->findOneBy('product', ['WHERE' => "id = {$gallery->getProduct()}"]);

        /** @var Image $image */
        $image = $this->manager->findOneBy('image', ['WHERE' => "id = {$gallery->getImage()}"]);

        $productData = [
            'id' => $product->getId(),
            'image' => $image->getPath()
        ];

        $imageData = [
            'id' => $image->getId(),
            'name' => $product->getImage(),
            'path' => $product->getImage()
        ];

        if (false === $this->manager->update('product', $productData)) {
            $this->notifications->addDanger('Mise à jour impossible', $this->manager->getError());

            return false;
        }

        if (false === $this->manager->update('image', $imageData)) {
            $this->notifications->addDanger('Mise à jour impossible', $this->manager->getError());

            return false;
        }

        $this->notifications->addSuccess('Mise à jour réussie', 'L\'image mise en avant a été mise à jour.');

        return json_encode(['productId' => $product->getId(), 'galleryId' => $galleryId]);
    }
}
