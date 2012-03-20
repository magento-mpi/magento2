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

define('COUNT_CUSTOMER_ADDRESES', 3);

$fixturesDir = realpath(dirname(__FILE__) . '/../../../../../fixtures');

/* @var $customerFixture Mage_Customer_Model_Customer */
$customerFixture = require $fixturesDir . '/Customer/Customer.php';

/* @var $customerAddressFixture Mage_Customer_Model_Address */
$customerAddressFixture = require $fixturesDir . '/Customer/Address.php';

// Create customer
$customerFixture->save();

// Get address eav required attributes
$requiredAttributes = array();
foreach (Mage::getModel('customer/address')->getAttributes() as $attribute) {
    if ($attribute->getIsRequired() && $attribute->getIsVisible()) {
        $requiredAttributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
    }
}

// Create addresses
for ($i = 0; $i < COUNT_CUSTOMER_ADDRESES; $i++) {
    $address = clone $customerAddressFixture;
    $address->setCustomer($customerFixture);
    foreach ($requiredAttributes as $requiredAttributes => $requiredAttribute) {
        if (!in_array($requiredAttributes, array('country_id', 'region'))) {
            $requiredAttribute .= uniqid();
        }
        $address->setData($attributeCode, $requiredAttribute);
    }
    $address->save();
}

Magento_Test_Webservice::setFixture('customer',
    Mage::getModel('customer/customer')->load($customerFixture->getId())); // for load addresses collection
