<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Paypal
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$recurringProfile = Mage::getModel('Mage_Sales_Model_Recurring_Profile');
$recurringProfile->addData(array(
    'store_id'                => 1,
    'method_code'             => Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS,
    'reference_id'            => 'I-C76MC3FM2HBX',
    'internal_reference_id'   => '5-33949e201adc4b03fbbceafccba893ce',
    'schedule_description'    => 'Recurring Profile',
    'start_date_is_editable'  => '0',
    'period_unit'             => 'day',
    'period_frequency'        => '1',
    'period_max_cycles'       => '3',
    'trial_period_unit'       => 'day',
    'trial_period_frequency'  => '1',
    'trial_period_max_cycles' => '3',
    'trial_billing_amount'    => '100.0000',
    'init_amount'             => '100.0000',
    'billing_amount'          => '100.0000',
    'currency_code'           => 'USD',
    'order_info'              => array('base_currency_code' => 'USD'),
    'order_item_info'         => serialize('item info'),
    'billing_address_info'    => serialize('billing address info'),
));
$recurringProfile->save();
