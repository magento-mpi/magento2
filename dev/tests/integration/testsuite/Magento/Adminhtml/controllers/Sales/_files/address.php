<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var $address Magento_Sales_Model_Order_Address */
$address = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order_Address');
$address->setRegion('CA')
    ->setPostcode('90210')
    ->setFirstname('a_unique_firstname')
    ->setLastname('lastname')
    ->setStreet('street')
    ->setCity('Beverly Hills')
    ->setEmail('admin@example.com')
    ->setTelephone('1111111111')
    ->setCountryId('US')
    ->setAddressType('shipping')
    ->save();
