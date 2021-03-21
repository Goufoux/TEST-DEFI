<?php

namespace Form;

use Core\Form;

class ProductForm extends Form 
{
    const data = [
        'name' => [
            'required' => true,
            'length' => [3, 80]
        ],
        'chapo' => [
            'length' => [0, 35]
        ],
        'meta_title' => [
            'required' => true,
            'length' => [3, 80]
        ],
        'meta_description' => [
            'required' => true,
            'length' => [10, 165]
        ],
        'content' => [
            'required' => true,
            'length' => [10, 500000]
        ],
        'alt' => [
            'length' => [3, 50] 
        ]
    ];
    
    public function verif(array $data)
    {
        $this->requiredControl(self::data, $data);
        $this->launch(self::data, $data);
    }
}
