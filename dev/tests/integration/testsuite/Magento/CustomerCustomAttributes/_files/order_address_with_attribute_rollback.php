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

/** @var $address \Magento\Sales\Model\Order\Address */
$address = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Sales\Model\Order\Address');
$address->load('admin@example.com', 'email');
$address->delete();

/** @var $attribute \Magento\Customer\Model\Attribute */
$attribute = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Customer\Model\Attribute');
$attribute->loadByCode('customer_address', 'fixture_address_attribute');
$attribute->delete();
