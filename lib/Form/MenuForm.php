<?php

namespace Form;

use Core\Form;

class MenuForm extends Form
{
    const data = [
        'name' => [
            'required' => true,
            'length' => [3, 45]
        ],
        'link' => [
            'length' => [3, 255]
        ]
    ];
    
    public function verif(array $data)
    {
        $this->requiredControl(self::data, $data);
        $this->launch(self::data, $data);
    }
}
