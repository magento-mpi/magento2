<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $address Magento_Sales_Model_Order_Address */
$address = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Sales_Model_Order_Address');
$address->load('admin@example.com', 'email');
$address->delete();

/** @var $attribute Magento_Customer_Model_Attribute */
$attribute = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Customer_Model_Attribute');
$attribute->loadByCode('customer_address', 'fixture_address_attribute');
$attribute->delete();
