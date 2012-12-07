<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$customer = new Mage_Customer_Model_Customer();
$customer->setStoreId(1)
    ->setCreatedIn('Default Store View')
    ->setDefaultBilling(1)
    ->setDefaultShipping(1)
    ->setEmail('mr.test.creditmemo.' . uniqid() . '@test.com')
    ->setFirstname('Test')
    ->setLastname('Test')
    ->setMiddlename('Test')
    ->setGroupId(1)
    ->setRewardUpdateNotification(1)
    ->setRewardWarningNotification(1)
    ->save();
Magento_Test_Webservice::setFixture('customer', $customer, Magento_Test_Webservice::AUTO_TEAR_DOWN_DISABLED);

$customerAddress = new Mage_Customer_Model_Address();
$customerAddress->setData(array(
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
$customerAddress->setCustomer($customer);
$customerAddress->save();
Magento_Test_Webservice::setFixture('customer_address', $customerAddress,
    Magento_Test_Webservice::AUTO_TEAR_DOWN_DISABLED);

//Set customer default shipping and billing address
$customer->addAddress($customerAddress);
$customer->setDefaultShipping($customerAddress->getId());
$customer->setDefaultBilling($customerAddress->getId());
$customer->save();
