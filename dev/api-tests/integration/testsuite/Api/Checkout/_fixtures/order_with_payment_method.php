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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$fixturesDir = realpath(dirname(__FILE__) . '/../../../../fixture');

/* @var $customerFixture Mage_Customer_Model_Customer */
$customerFixture = require $fixturesDir . '/_block/Customer/Customer.php';

/* @var $customerAddressFixture Mage_Customer_Model_Address */
$customerAddressFixture = require $fixturesDir . '/_block/Customer/Address.php';
$customerFixture->save();

// Get address eav required attributes
$requiredAttributes = array();
foreach (Mage::getModel('Mage_Customer_Model_Address')->getAttributes() as $attribute) {
    if ($attribute->getIsRequired() && $attribute->getIsVisible()) {
        $requiredAttributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
    }
}

$address = clone $customerAddressFixture;
$address->setCustomer($customerFixture);
$address->addData(array(
    'city'                => 'New York',
    'country_id'          => 'US',
    'fax'                 => '56-987-987',
    'firstname'           => 'Jacklin',
    'lastname'            => 'Sparrow',
    'middlename'          => 'John',
    'postcode'            => '10012',
    'region'              => 'New York',
    'region_id'           => '43',
    'street'              => 'Main Street',
    'telephone'           => '718-452-9207',
    'is_default_billing'  => true,
    'is_default_shipping' => true
));
$address->save();

$customerFixture->addAddress($address);
$customerFixture->setDefaultShipping($address->getId());
$customerFixture->setDefaultBilling($address->getId());
$customerFixture->save();


Magento_Test_Webservice::setFixture('customer',
    Mage::getModel('Mage_Customer_Model_Customer')->load($customerFixture->getId()),
    Magento_Test_Webservice::AUTO_TEAR_DOWN_DISABLED); // for load addresses collection

//Set up simple product fixture
require_once 'product_simple.php';
/** @var $product Mage_Catalog_Model_Product */
$product = Magento_Test_Webservice::getFixture('product_simple');


//Create quote
$quote = new Mage_Sales_Model_Quote();
$quote->setStoreId(1)
    ->setIsActive(false)
    ->setIsMultiShipping(false)
    ->assignCustomerWithAddressChange($customerFixture)
    ->setCheckoutMethod($customerFixture->getMode())
    ->setPasswordHash($customerFixture->encryptPassword($customerFixture->getPassword()))
    ->addProduct($product->load($product->getId()), 2);

$quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');
$quote->getPayment()->setMethod('ccsave');

$quote->collectTotals();
$quote->save();
Magento_Test_Webservice::setFixture('quote', $quote, Magento_Test_Webservice::AUTO_TEAR_DOWN_DISABLED);

//Create order
$quoteService = new Mage_Sales_Model_Service_Quote($quote);
//Set payment method to check/money order
$quoteService->getQuote()->getPayment()->setMethod('ccsave');

Magento_Test_Webservice::setFixture('order', $order, Magento_Test_Webservice::AUTO_TEAR_DOWN_DISABLED);
