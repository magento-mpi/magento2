<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$address = new Mage_Sales_Model_Order_Address();
$address->load('admin@example.com', 'email');
$address->delete();

$attribute = new Mage_Customer_Model_Attribute();
$attribute->loadByCode('customer_address', 'fixture_address_attribute');
$attribute->delete();
