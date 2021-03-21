<?php

namespace Form;

use Core\Form;

class MenuGroupForm extends Form
{
    const data = [
        'name' => [
            'required' => true,
            'length' => [3, 80]
        ]
    ];
    
    public function verif(array $data)
    {
        $this->requiredControl(self::data, $data);
        $this->launch(self::data, $data);
    }
}
