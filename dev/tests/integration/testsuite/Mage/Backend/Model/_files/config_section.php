<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(array ('section' => 'paypal', 'groups' =>array(
        'account' => array (
            'fields' => array (
                'merchant_country' => array ('value' => 'US'),
                'business_account' => array ('value' => 'owner@example.com'),
            ),
        ),
        'global' => array (
            'fields' => array (
                'payflow_link' => array ('value' => '1'),
            ),
        ),
        'payflow_link' => array (
            'fields' => array (
                'partner' => array ('value' => 'link_partner'),
                'vendor' => array ('value' => 'link_vendor'),
                'user' => array ('value' => 'link_user'),
                'pwd' => array ('value' => 'password'),
            ),
        ),
    ),
    'expected' => array(
        'paypal' => array(
            'paypal/general/business_account' => 'owner@example.com',
            'paypal/general/merchant_country' => 'US'
        ),
        'payment/payflow_link' => array(
            'payment/payflow_link/active' => '1',
            'payment/payflow_link/partner' => 'link_partner',
            'payment/payflow_link/vendor' => 'link_vendor',
            'payment/payflow_link/user' => 'link_user',
            'payment/payflow_link/pwd' => 'password',
        )
    )
));
