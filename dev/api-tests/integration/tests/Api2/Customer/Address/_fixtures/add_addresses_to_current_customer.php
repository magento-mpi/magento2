<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define('COUNT_CUSTOMER_ADDRESES_FOR_CURRENT_CUSTOMER', 3);

$fixturesDir = realpath(dirname(__FILE__) . '/../../../../../fixtures');

/* @var $customerAddressFixture Mage_Customer_Model_Address */
$customerAddressFixture = require $fixturesDir . '/Customer/Address.php';

// Load current customer
/* @var $customer Mage_Customer_Model_Customer */
$customer = Mage::getModel('customer/customer');
$customer->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail(TESTS_CUSTOMER_EMAIL);

// Get address eav required attributes
$requiredAttributes = array();
foreach (Mage::getModel('customer/address')->getAttributes() as $attribute) {
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

Magento_Test_Webservice::setFixture('addresses', $addresses);
