<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'data' => array(
        'default' => array(
            'payment' => array(
                'payment_method' => array(
                    'active' => 0,
                    'debug' => 0,
                    'email_customer' => 0,
                    'login' => null,
                    'merchant_email' => null,
                    'order_status' => 'processing',
                    'trans_key' => null,
                ),
            ),
        ),
    ),
    'metadata' => array(
        'payment/payment_method/login' => array(
            'backendModel' => 'Custom_Backend_Model_Config_Backend_Encrypted',
        ),
        'payment/payment_method/trans_key' => array(
            'backendModel' => 'Custom_Backend_Model_Config_Backend_Encrypted',
        ),
    ),
);
