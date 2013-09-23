<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var Magento_SalesRule_Model_Rule $salesRule */
$salesRule = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_SalesRule_Model_Rule');
$salesRule->setData(array(
    'name' => '50% Off on Large Orders',
    'is_active' => 1,
    'customer_group_ids' => array(Magento_Customer_Model_Group::NOT_LOGGED_IN_ID),
    'coupon_type' => Magento_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON,
    'conditions' => array(
        array(
            'type' => 'Magento_SalesRule_Model_Rule_Condition_Address',
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
