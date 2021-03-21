<?php

namespace Form;

use Core\Form;

class RubriqueForm extends Form
{
    const data = [
        'name' => [
            'required' => true,
            'length' => [3, 45]
        ],
        'description' => [
            'required' => true,
            'length' => [3, 50000]
        ],
        'meta_title' => [
            'required' => true,
            'length' => [3, 85]
        ],
        'meta_description' => [
            'required' => true,
            'length' => [10, 165]
        ]
    ];
    
    public function verif(array $data)
    {
        $this->requiredControl(self::data, $data);
        $this->launch(self::data, $data);
    }
}