<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(array ('section' => 'payment', 'groups' =>array(
// TODO: This piece of code should be uncommented after revert of changes described in MPI-1023 comments
//        'account' => array (
//            'fields' => array (
//                'merchant_country' => array ('value' => 'US'),
//            ),
//        ),
        'paypal_payments' => array(
            'groups' => array(
                'payflow_advanced' => array(
                    'groups' => array(
                        'required_settings' => array(
                            'groups' => array(
                                'payments_advanced' => array(
                                    'fields' => array(
                                        'business_account' => array ('value' => 'owner@example.com')
                                    )
                                )
                            )
                        )
                    )
                ),
                'payflow_link' => array(
                    'groups' => array(
                        'payflow_link_required' => array(
                            'fields' => array(
                                'enable_payflow_link' => array('value' => '1')
                            ),
                            'groups' => array(
                                'payflow_link_payflow_link' => array(
                                    'fields' => array(
                                        'partner' => array ('value' => 'link_partner'),
                                        'vendor' => array ('value' => 'link_vendor'),
                                        'user' => array ('value' => 'link_user'),
                                        'pwd' => array ('value' => 'password'),
                                    )
                                )
                            )
                        )
                    )
                )
            )
        )
    ),
    'expected' => array(
        'paypal' => array(
            'paypal/general/business_account' => 'owner@example.com',
// TODO: This piece of code should be uncommented after revert of changes described in MPI-1023 comments 
//            'paypal/general/merchant_country' => 'US'
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
