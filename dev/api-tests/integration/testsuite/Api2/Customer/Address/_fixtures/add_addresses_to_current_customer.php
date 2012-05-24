<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

define('COUNT_CUSTOMER_ADDRESES_FOR_CURRENT_CUSTOMER', 3);

$fixturesDir = realpath(dirname(__FILE__) . '/../../../../../fixtures');

/* @var $customerAddressFixture Mage_Customer_Model_Address */
$customerAddressFixture = require $fixturesDir . '/Customer/Address.php';

// Load current customer
/* @var $customer Mage_Customer_Model_Customer */
$customer = Mage::getModel('Mage_Customer_Model_Customer');
$customer->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail(TESTS_CUSTOMER_EMAIL);

// Get address eav required attributes
$requiredAttributes = array();
foreach (Mage::getModel('Mage_Customer_Model_Address')->getAttributes() as $attribute) {
    if ($attribute->getIsRequired() && $attribute->getIsVisible()) {
        $requiredAttributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
    }
}

// Create addresses
$addresses = array();
for ($i = 0; $i < COUNT_CUSTOMER_ADDRESES_FOR_CURRENT_CUSTOMER; $i++) {
    $address = clone $customerAddressFixture;
    $address->setCustomer($customer);
    foreach ($requiredAttributes as $requiredAttributes => $requiredAttribute) {
        if (!in_array($requiredAttributes, array('country_id', 'region'))) {
            $requiredAttribute .= uniqid();
        }
        $address->setData($attributeCode, $requiredAttribute);
    }
    $address->save();
    $addresses[] = $address;
}

Magento_Test_Webservice::setFixture('addresses', $addresses, Magento_Test_Webservice::AUTO_TEAR_DOWN_DISABLED);
