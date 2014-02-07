<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
$customer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Customer\Model\Customer');
$customer
    ->setWebsiteId(1)
    ->setId(5)
    ->setEntityTypeId(1)
    ->setAttributeSetId(1)
    ->setEmail('customer5@example.com')
    ->setPassword('password')
    ->setGroupId(1)
    ->setStoreId(1)
    ->setIsActive(1)
    ->setFirstname('Firstname')
    ->setLastname('Lastname')
;
$customer->isObjectNew(true);
$customer->save();
