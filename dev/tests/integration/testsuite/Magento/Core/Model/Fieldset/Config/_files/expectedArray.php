<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'global' => array(
        'sales_convert_quote_address' => array(
            'company' => array(
                'to_order_address' => '*',
                'to_customer_address' => '*'
            ),
            'street_full' => array(
                'to_order_address' => 'street'
            ),
            'street' => array(
                'to_customer_address' => '*'
            )
        )
    )
);