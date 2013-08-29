<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'global' => array(
        'fieldsets' => array(
            'customer_account' => array(
                'prefix' => array(
                    'create' => '1',
                    'update' => '1',
                    'name' => '1',
                ),
                'firstname' => array(
                    'create' => '1',
                    'update' => '1',
                    'name' => '1',
                ),
                'middlename' => array(
                    'create' => '1',
                    'update' => '1',
                    'name' => '1',
                ),
                'lastname' => array(
                    'create' => '1',
                    'update' => '1',
                    'name' => '1',
                ),
                'suffix' => array(
                    'create' => '1',
                    'update' => '1',
                    'name' => '1',
                ),
                'email' => array(
                    'create' => '1',
                    'update' => '1',
                ),
                'password' => array(
                    'create' => '1',
                ),
                'confirmation' => array(
                    'create' => '1',
                ),
                'dob' => array(
                    'create' => '1',
                    'update' => '1',
                ),
                'taxvat' => array(
                    'create' => '1',
                    'update' => '1',
                ),
                'gender' => array(
                    'create' => '1',
                    'update' => '1',
                ),
            ),
            'customer_address' => array(
                'vat_id' => array(
                    'to_quote_address' => '*'
                ),
                'vat_is_valid' => array(
                    'to_quote_address' => '*'
                ),
                'vat_request_id' => array(
                    'to_quote_address' => '*'
                ),
                'vat_request_date' => array(
                    'to_quote_address' => '*'
                ),
                'vat_request_success' => array(
                    'to_quote_address' => '*'
                ),
            )
        )
    )
);
