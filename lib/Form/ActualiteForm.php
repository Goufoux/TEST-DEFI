<?php

namespace Form;

use Core\Form;

class ActualiteForm extends Form
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
        'chapo' => [
            'length' => [0, 125]
        ],
        'content' => [
            'required' => true,
            'length' => [10, 500000]
        ],
        'alt' => [
            'length' => [3, 150]
        ]
    ];
    
    public function verif(array $data)
    {
        $this->requiredControl(self::data, $data);
        $this->launch(self::data, $data);
    }
}
