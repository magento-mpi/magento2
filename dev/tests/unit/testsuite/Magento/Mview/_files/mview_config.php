<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'inputXML' => '<?xml version="1.0" encoding="UTF-8"?><config><view id="view_one" class="Ogogo\Class\One">'
        . '<subscriptions><table name="some_entity" entity_column="entity_id" />'
        . '<table name="some_product_relation" entity_column="product_id" /></subscriptions></view></config>',
    'expected' => array(
        'view_one' => array(
            'view_id'       => 'view_one',
            'action_class'  => 'Ogogo\Class\One',
            'subscriptions' => array(
                'some_entity' => array(
                    'name'   => 'some_entity',
                    'column' => 'entity_id',
                ),
                'some_product_relation' => array(
                    'name'   => 'some_product_relation',
                    'column' => 'product_id',
                ),
            ),
        ),
    ),
);
