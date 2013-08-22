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

$customer = Mage::getModel('Magento_Customer_Model_Customer');

$customer->setWebsiteId(1)
    ->setEntityId(1)
    ->setEntityTypeId(1)
    ->setAttributeSetId(0)
    ->setEmail('customer@example.com')
    ->setPassword('password')
    ->setGroupId(1)
    ->setStoreId(1)
    ->setIsActive(1)
    ->setFirstname('Firstname')
    ->setLastname('Lastname')
    ->setDefaultBilling(1)
    ->setDefaultShipping(1);
$customer->isObjectNew(true);
$customer->save();
$customers[] = $customer;

$customer = Mage::getModel('Magento_Customer_Model_Customer');
$customer->setWebsiteId(1)
    ->setEntityId(2)
    ->setEntityTypeId(1)
    ->setAttributeSetId(0)
    ->setEmail('julie.worrell@example.com')
    ->setPassword('password')
    ->setGroupId(1)
    ->setStoreId(1)
    ->setIsActive(1)
    ->setFirstname('Julie')
    ->setLastname('Worrell')
    ->setDefaultBilling(1)
    ->setDefaultShipping(1);
$customer->isObjectNew(true);
$customer->save();
$customers[] = $customer;

$customer = Mage::getModel('Magento_Customer_Model_Customer');
$customer->setWebsiteId(1)
    ->setEntityId(3)
    ->setEntityTypeId(1)
    ->setAttributeSetId(0)
    ->setEmail('david.lamar@example.com')
    ->setPassword('password')
    ->setGroupId(1)
    ->setStoreId(1)
    ->setIsActive(1)
    ->setFirstname('David')
    ->setLastname('Lamar')
    ->setDefaultBilling(1)
    ->setDefaultShipping(1);
$customer->isObjectNew(true);
$customer->save();
$customers[] = $customer;

Mage::unregister('_fixture/Magento_ImportExport_Customer_Collection');
Mage::register('_fixture/Magento_ImportExport_Customer_Collection', $customers);
