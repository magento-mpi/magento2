<?php
/**
 * SalesRule 10% discount coupon
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var Magento_SalesRule_Model_Rule $salesRule */
$salesRule = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_SalesRule_Model_Rule');

$data = array(
    'name' => 'Test Coupon',
    'is_active' => true,
    'website_ids' => array(
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_StoreManagerInterface')
            ->getStore()->getWebsiteId()
    ),
    'customer_group_ids' => array(Magento_Customer_Model_Group::NOT_LOGGED_IN_ID),
    'coupon_type' => Magento_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC,
    'coupon_code' => uniqid(),
    'simple_action' => Magento_SalesRule_Model_Rule::BY_PERCENT_ACTION,
    'discount_amount' => 10,
    'discount_step' => 1,
);

$salesRule->loadPost($data)->setUseAutoGeneration(false)->save();
