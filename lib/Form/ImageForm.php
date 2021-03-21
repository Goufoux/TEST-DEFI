<?php

namespace Form;

use Core\Form;

class ImageForm extends Form
{
    const data = [
        'name' => [
            'required' => true,
            'length' => [3,75]
        ],
        'description' => [
            'length' => [0, 500]
        ],
        'path' => [
            'required' => true
        ],
        'alt' => [
            'required' => true
        ]
    ];

    public function verif($data)
    {
        $this->requiredControl(self::data, $data);
        $this->launch(self::data, $data);
    }
}
