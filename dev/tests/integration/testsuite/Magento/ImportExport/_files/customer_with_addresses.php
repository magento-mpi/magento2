<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
$customers = array();

//Create customer
$customer = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Customer_Model_Customer');
$customer->setWebsiteId(1)
    ->setEntityId(1)
    ->setEntityTypeId(1)
    ->setAttributeSetId(0)
    ->setEmail('BetsyParker@example.com')
    ->setPassword('password')
    ->setGroupId(1)
    ->setStoreId(1)
    ->setIsActive(1)
    ->setFirstname('Betsy')
    ->setLastname('Parker')
    ->setGender(2);
$customer->isObjectNew(true);

// Create address
$address = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Customer_Model_Address');
//  default_billing and default_shipping information would not be saved, it is needed only for simple check
$address->addData(array(
    'firstname'         => 'Betsy',
    'lastname'          => 'Parker',
    'street'            => '1079 Rocky Road',
    'city'              => 'Philadelphia',
    'country_id'        => 'US',
    'region_id'         => '51',
    'postcode'          => '19107',
    'telephone'         => '215-629-9720',
    'default_billing'   => 1,
    'default_shipping'  => 1,
));

// Assign customer and address
$customer->addAddress($address);
$customer->save();

// Mark last address as default billing and default shipping for current customer
$customer->setDefaultBilling($address->getId());
$customer->setDefaultShipping($address->getId());
$customer->save();

$customers[] = $customer;

$customer = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Customer_Model_Customer');
$customer->setWebsiteId(1)
    ->setEntityId(2)
    ->setEntityTypeId(1)
    ->setAttributeSetId(0)
    ->setEmail('AnthonyNealy@example.com')
    ->setPassword('password')
    ->setGroupId(1)
    ->setStoreId(1)
    ->setIsActive(1)
    ->setFirstname('Anthony')
    ->setLastname('Nealy')
    ->setGender(1);
$customer->isObjectNew(true);

$address = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Customer_Model_Address');
$address->addData(array(
    'firstname'         => 'Anthony',
    'lastname'          => 'Nealy',
    'street'            => '3176 Cambridge Court',
    'city'              => 'Fayetteville',
    'country_id'        => 'US',
    'region_id'         => '5',
    'postcode'          => '72701',
    'telephone'         => '479-899-9849',
    'default_billing'   => 0,
    'default_shipping'  => 0
));
$customer->addAddress($address);

$address = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Customer_Model_Address');
$address->addData(array(
    'firstname'         => 'Anthony',
    'lastname'          => 'Nealy',
    'street'            => '4709 Pleasant Hill Road',
    'city'              => 'Irvine',
    'country_id'        => 'US',
    'region_id'         => '12',
    'postcode'          => '92664',
    'telephone'         => '562-208-2310',
    'default_billing'   => 1,
    'default_shipping'  => 1
));
$customer->addAddress($address);

$customer->save();

$customer->setDefaultBilling($address->getId());
$customer->setDefaultShipping($address->getId());
$customer->save();

$customers[] = $customer;

$customer = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Customer_Model_Customer');
$customer->setWebsiteId(1)
    ->setEntityId(3)
    ->setEntityTypeId(1)
    ->setAttributeSetId(0)
    ->setEmail('LoriBanks@example.com')
    ->setPassword('password')
    ->setGroupId(1)
    ->setStoreId(1)
    ->setIsActive(1)
    ->setFirstname('Lori')
    ->setLastname('Banks')
    ->setGender(2);
$customer->isObjectNew(true);

$address = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Customer_Model_Address');
$address->addData(array(
    'firstname'         => 'Lori',
    'lastname'          => 'Banks',
    'street'            => '2573 Goodwin Avenue',
    'city'              => 'Wenatchee',
    'country_id'        => 'US',
    'region_id'         => '62',
    'postcode'          => '98801',
    'telephone'         => '509-421-4364',
    'default_billing'   => 1,
    'default_shipping'  => 1,
));
$customer->addAddress($address);
$customer->save();

$customer->setDefaultBilling($address->getId());
$customer->setDefaultShipping($address->getId());
$customer->save();

$customers[] = $customer;

/** @var $objectManager Magento_TestFramework_ObjectManager */
$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
$objectManager->get('Magento_Core_Model_Registry')
    ->unregister('_fixture/Magento_ImportExport_Customers_Array');
$objectManager->get('Magento_Core_Model_Registry')
    ->register('_fixture/Magento_ImportExport_Customers_Array', $customers);
