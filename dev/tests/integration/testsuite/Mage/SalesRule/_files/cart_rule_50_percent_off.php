<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var Mage_SalesRule_Model_Rule $salesRule */
$salesRule = Mage::getModel('Mage_SalesRule_Model_Rule');
$salesRule->setData(array(
    'name' => '50% Off on Large Orders',
    'is_active' => 1,
    'customer_group_ids' => array(0/*Not Logged In*/),
    'coupon_type' => 1/*No Coupon*/,
    'conditions' => array(
        array(
            'type' => 'Mage_SalesRule_Model_Rule_Condition_Address',
            'attribute' => 'base_subtotal',
            'operator' => '>',
            'value' => 1000,
        ),
    ),
    'simple_action' => 'by_percent',
    'discount_amount' => 50,
    'stop_rules_processing' => 1,
    'website_ids' => array(Mage::app()->getWebsite()->getId()),
));
$salesRule->save();
