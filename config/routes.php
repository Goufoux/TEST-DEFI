<?php

$routes = [
    'backend' => [
        '/admin/' => [
            'name' => 'app_index_index'
        ],
        '/admin/restore' => [
            'name' => 'app_index_restore'
        ],
        '/admin/contenu/' => [
            'name' => 'app_contenu_index',
            'module' => 'Contenu',
            'view' => 'Liste'
        ],
        '/admin/contenu/update/{id}' => [
            'name' => 'app_contenu_update',
            'int' => 'id',
            'module' => 'Contenu',
            'view' => 'Mise à jour'
        ],
        '/admin/contenu/remove/{id}' => [
            'name' => 'app_contenu_remove',
            'int' => 'id'
        ],
        '/admin/contenu/new' => [
            'name' => 'app_contenu_new',
            'module' => 'Contenu',
            'view' => 'Nouveau'
        ],
        '/admin/menu/update/{id}' => [
            'name' => 'app_menu_update',
            'int' => 'id',
            'module' => 'Menu',
            'view' => 'Mise à jour'
        ],
        '/admin/menu/' => [
            'name' => 'app_menu_index',
            'module' => 'Menu',
            'view' => 'Liste'
        ],
        '/admin/menu/new' => [
            'name' => 'app_menu_new',
            'module' => 'Menu',
            'view' => 'Nouveau'
        ],
        '/admin/uploader/' => [
            'name' => 'app_uploader_index',
            'module' => 'Uploader',
            'view' => 'Liste'
        ],
        '/admin/uploader/new' => [
            'name' => 'app_uploader_new',
            'module' => 'Uploader',
            'view' => 'Nouveau'
        ],
        '/admin/uploader/remove/{id}' => [
            'name' => 'app_uploader_remove',
            'int' => 'id'
        ],
        '/admin/menu/group/' => [
            'name' => 'app_menugroup_index',
            'module' => 'Groupe de menu',
            'view' => 'Liste'
        ],
        '/admin/menu/remove/group/{id}' => [
            'name' => 'app_menu_removegroup',
            'int' => 'id'
        ],
        '/admin/menu/remove/contenu/{id}' => [
            'name' => 'app_menu_removecontenu',
            'int' => 'id'
        ],
        '/admin/menu/remove/{id}' => [
            'name' => 'app_menu_remove',
            'int' => 'id'
        ],
        '/admin/menu/group/new' => [
            'name' => 'app_menugroup_new',
            'module' => 'Groupe de menu',
            'view' => 'Nouveau'
        ],
        '/admin/menu/group/remove/{id}' => [
            'name' => 'app_menugroup_remove',
            'int' => 'id'
        ],
        '/admin/product/' => [
            'name' => 'app_product_index',
            'module' => 'Produit',
            'view' => 'Liste'
        ],
        '/admin/product/new' => [
            'name' => 'app_product_new',
            'module' => 'Produit',
            'view' => 'Nouveau'
        ],
        '/admin/product/search' => [
            'name' => 'app_product_search'
        ],
        '/admin/product/update/{id}' => [
            'name' => 'app_product_update',
            'int' => 'id',
            'module' => 'Produit',
            'view' => 'Mise à jour'
        ],
        '/admin/product/remove/{id}' => [
            'name' => 'app_product_remove',
            'int' => 'id'
        ],
        '/admin/actualite/' => [
            'name' => 'app_actualite_index',
            'module' => 'Actualité',
            'view' => 'Liste'
        ],
        '/admin/actualite/new' => [
            'name' => 'app_actualite_new',
            'module' => 'Actualité',
            'view' => 'Nouveau'
        ],
        '/admin/actualite/update/{id}' => [
            'name' => 'app_actualite_update',
            'int' => 'id',
            'module' => 'Actualité',
            'view' => 'Mise à jour'
        ],
        '/admin/actualite/archiver/{id}' => [
            'name' => 'app_actualite_archiverActualite',
            'int' => 'id'
        ],
        '/admin/actualite/archiver/reverse/{id}' => [
            'name' => 'app_actualite_desarchiverActualite',
            'int' => 'id'
        ],
        '/admin/actualite/archive' => [
            'name' => 'app_actualite_archive'
        ],
        '/admin/actualite/remove/{id}' => [
            'name' => 'app_actualite_remove',
            'int' => 'id'
        ],
        '/admin/actualite/update/homepage/{id}/{value}' => [
            'name' => 'app_actualite_updateHomepage',
            'int' => 'id',
            'integer' => 'value'
        ],
        '/admin/actualite/imageLink/{id}' => [
            'name' => 'app_actualite_imageLink',
            'int' => 'id'
        ],
        '/admin/rubrique/' => [
            'name' => 'app_rubrique_index',
            'module' => 'Rubrique',
            'view' => 'Liste'
        ],
        'admin/rubrique/new' => [
            'name' => 'app_rubrique_new',
            'module' => 'Rubrique',
            'view' => 'Nouveau'
        ],
        '/admin/rubrique/remove/{id}' => [
            'name' => 'app_rubrique_remove',
            'int' => 'id'
        ],
        '/admin/rubrique/update/{slug}' => [
            'name' => 'app_rubrique_update',
            'string' => 'slug',
            'module' => 'Rubrique',
            'view' => 'Mise à jour'
        ],
        '/admin/product/imageLink/{slug}' => [
            'name' => 'app_product_imageLink',
            'string' => 'slug'
        ],
        '/admin/product/remove/image/{id}' => [
            'name' => 'app_product_removeImage',
            'int' => 'id'
        ],
        '/admin/formulaire/' => [
            'name' => 'app_formulaire_index',
            'module' => 'Formulaire',
            'view' => 'Liste'
        ],
        '/admin/formulaire/{id}' => [
            'name' => 'app_formulaire_view',
            'int' => 'id',
            'module' => 'Formulaire',
            'view' => 'Détails'
        ],
        '/admin/formulaire/remove/{id}' => [
            'name' => 'app_formulaire_remove',
            'int' => 'id'
        ],
        '/admin/connexion/' => [
            'name' => 'app_connexion_index',
            'module' => 'Connexion',
            'view' => 'Liste'
        ],
        '/admin/connexion/truncate' => [
            'name' => 'app_connexion_truncate'
        ],
        '/admin/product/mev/product/{id}' => [
            'name' => 'app_product_mevProduct',
            'int' => 'id'
        ],
        '/admin/product/mev/gallery/{id}' => [
            'name' => 'app_product_mevGallery',
            'int' => 'id'
        ]
    ],
    'frontend' => [
        '/' => [
            'name' => 'app_index_index'
        ],
        '/connect/' => [
            'name' => 'app_connect_index'
        ],
        '/dimension' => [
            'name' => 'app_index_dimension'
        ],
        '/ajax/notifications' => [
            'name' => 'app_ajax_notifications'
        ],
        '/logout' => [
            'name' => 'app_index_logout'
        ],
        '/view/{id}' => [
            'name' => 'app_index_view',
            'int' => 'id'
        ],
        '/contenu/{id}/{slug}' => [
            'name' => 'app_contenu_contenu',
            'int' => 'id',
            'string' => 'slug'
        ],
        '/tinymce/flmngr' => [
            'name' => 'app_tinymce_flmngr'
        ],
        '/menu/' => [
            'name' => 'app_menu_index'
        ],
        '/product/{id}/{slug}' => [
            'name' => 'app_product_view',
            'int' => 'id',
            'string' => 'slug'
        ],
        '/tinymce/{id}/galleries' => [
            'name' => 'app_tinymce_galleries',
            'int' => 'id'
        ],
        '/actualite' => [
            'name' => 'app_actualite_index'
        ],
        '/actualite/id/{id}/{slug}' => [
            'name' => 'app_actualite_view',
            'int' => 'id',
            'string' => 'slug'
        ],
        '/tinymce/upload' => [
            'name' => 'app_tinymce_upload'
        ],
        '/rubrique/{slug}' => [
            'name' => 'app_rubrique_view',
            'string' => 'slug'
        ],
        '/contact' => [
            'name' => 'app_index_contact'
        ]
    ]
];

return $routes;