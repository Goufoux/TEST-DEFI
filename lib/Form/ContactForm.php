<?php

namespace Form;

use Core\Form;

class ContactForm extends Form
{
    const data = [
        'gender' => [
            'required' => true
        ],
        'name' => [
            'required' => true,
            'length' => [3, 35]
        ],
        'first_name' => [
            'required' => true,
            'length' => [3, 35]
        ],
        'company' => [
            'required' => true,
            'length' => [3, 75]
        ],
        'address' => [
            'required' => true,
            'length' => [3, 100]
        ],
        'code_postal' => [
            'required' => true,
            'length' => [3, 10]
        ],
        'city' => [
            'required' => true,
            'length' => [3, 75]
        ],
        'country' => [
            'required' => true,
            'length' => [3, 75]
        ],
        'phone' => [
            'required' => true,
            'length' => [10, 15]
        ],
        'email' => [
            'required' => true,
            'email' => null,
            'length' => [6, 100]
        ],
        'content' => [
            'required' => true,
            'length' => [15, 500]
        ],
        'provenance' => [
            'required' => true
        ]
    ];
    
    public function verif(array $data)
    {
        $this->requiredControl(self::data, $data);
        $this->launch(self::data, $data);
    }
}
