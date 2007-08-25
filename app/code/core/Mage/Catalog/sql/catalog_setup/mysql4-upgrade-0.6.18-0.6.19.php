<?php

$this->addAttribute('catalog_product', 'minimal_price', array(
                        'group'     => 'Prices',
                        'type'      => 'decimal',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Minimal Price',
                        'input'     => 'price',
                        'class'     => 'validate-number',
                        'source'    => '',
                        'global'    => false,
                        'visible'   => false,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
));