<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
$customer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Customer\Model\Customer');
/** @var Magento\Customer\Model\Customer $customer */
$customer->setWebsiteId(
    1
)->setId(
    1
)->setEntityTypeId(
    1
)->setAttributeSetId(
    1
)->setEmail(
    'customer@search.example.com'
)->setPassword(
    'password'
)->setGroupId(
    1
)->setStoreId(
    1
)->setIsActive(
    1
)->setFirstname(
    'Firstname'
)->setLastname(
    'Lastname'
)->setDefaultBilling(
    1
)->setDefaultShipping(
    1
)->setCreatedAt(
    '2014-02-28 15:52:26'
);
$customer->isObjectNew(true);

$customer->save();
$customer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Customer\Model\Customer');
$customer->setWebsiteId(
    1
)->setEntityId(
    2
)->setEntityTypeId(
    1
)->setAttributeSetId(
    0
)->setEmail(
    'customer2@search.example.com'
)->setPassword(
    'password'
)->setGroupId(
    1
)->setStoreId(
    1
)->setIsActive(
    1
)->setFirstname(
    'Firstname2'
)->setLastname(
    'Lastname2'
)->setDefaultBilling(
    1
)->setDefaultShipping(
    1
)->setCreatedAt(
    '2010-02-28 15:52:26'
);
$customer->isObjectNew(true);
$customer->save();

$customer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Customer\Model\Customer');
$customer->setWebsiteId(
    1
)->setEntityId(
    3
)->setEntityTypeId(
    1
)->setAttributeSetId(
    0
)->setEmail(
    'customer3@search.example.com'
)->setPassword(
    'password'
)->setGroupId(
    1
)->setStoreId(
    1
)->setIsActive(
    1
)->setFirstname(
    'Firstname3'
)->setLastname(
    'Lastname3'
)->setDefaultBilling(
    1
)->setDefaultShipping(
    1
)->setCreatedAt(
    '2012-02-28 15:52:26'
);
$customer->isObjectNew(true);
$customer->save();
