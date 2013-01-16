<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

/** @var $customer Mage_Customer_Model_Customer */
$customer = Mage::getModel('Mage_Customer_Model_Customer');
$customer->setId(1);
$customer->setStoreId(1)
    ->setWebsiteId(1)
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

/** @var $customerAddress Mage_Customer_Model_Address */
$customerAddress = Mage::getModel('Mage_Customer_Model_Address');
$customerAddress->setData(
    array(
        'city' => 'New York',
        'country_id' => 'US',
        'fax' => '56-987-987',
        'firstname' => 'Jacklin',
        'lastname' => 'Sparrow',
        'middlename' => 'John',
        'postcode' => '10012',
        'region' => 'New York',
        'region_id' => '43',
        'street' => 'Main Street',
        'telephone' => '718-452-9207',
        'is_default_billing' => true,
        'is_default_shipping' => true
    )
);
$customerAddress->setId(1);
$customerAddress->setCustomer($customer);
$customerAddress->save();

$customerAddress2 = Mage::getModel('Mage_Customer_Model_Address');
$customerAddress2->setData(
    array(
        'city' => 'Buffalo',
        'country_id' => 'US',
        'fax' => '56-987-987',
        'firstname' => 'Jacklin',
        'lastname' => 'Sparrow',
        'middlename' => 'John',
        'postcode' => '10012',
        'region' => 'New York',
        'region_id' => '43',
        'street' => '123 Main Street',
        'telephone' => '718-452-9207',
        'is_default_billing' => false,
        'is_default_shipping' => false
    )
);
$customerAddress2->setId(2);
$customerAddress2->setCustomer($customer);
$customerAddress2->save();

// Set customer default shipping and billing address
$customer->addAddress($customerAddress);
$customer->addAddress($customerAddress2);
$customer->setDefaultShipping($customerAddress->getId());
$customer->setDefaultBilling($customerAddress->getId());
$customer->save();
