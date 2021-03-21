<?php

namespace Form;

use Core\Form;

class ContenuForm extends Form
{
    const data = [
        'title' => [
            'required' => true,
            'length' => [3, 80]
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
            'length' => [10, 50000000]
        ]
    ];
    
    public function verif(array $data)
    {
        $this->requiredControl(self::data, $data);
        $this->launch(self::data, $data);
    }
}
