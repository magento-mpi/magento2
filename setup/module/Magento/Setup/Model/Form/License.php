<?php

namespace Magento\Setup\Model\Form;

use Zend\Form\Form;

class License extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('license');
        $this->setAttribute('method', 'post');
        $this->add([
            'name' => 'agree',
            'attributes' => [
                'type'     => 'Zend\Form\Element\Checkbox',
                'required' => '1',
            ],
            'options' => [
                'label' => 'I agree',
            ]
        ]);
        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type'     => 'button',
                'value' => 'Continue',
            ],
        ]);
    }
}
