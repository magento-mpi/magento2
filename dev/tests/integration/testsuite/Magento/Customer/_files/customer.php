<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var $repository \Magento\Customer\Api\CustomerRepositoryInterface */
$repository = $objectManager->create('\Magento\Customer\Api\CustomerRepositoryInterface');
/** @var \Magento\Customer\Api\Data\CustomerDataBuilder $builder */
$builder = $objectManager->create('\Magento\Customer\Api\Data\CustomerDataBuilder');
/** @var \Magento\Customer\Api\AccountManagementInterface $accountManagement */
$accountManagement = $objectManager->create('\Magento\Customer\Api\AccountManagementInterface');


$customer = $builder->setWebsiteId(1)
    ->setCustomAttribute('entity_type_id',1)
    ->setCustomAttribute('attribute_set_id', 1)
    ->setCustomAttribute('is_active', 1)
    ->setEmail('customer@example.com')
    ->setGroupId(1)
    ->setStoreId(1)
    ->setFirstname('John')
    ->setLastname('Smith')
    ->setDefaultBilling(1)
    ->setDefaultShipping(1)->create();

$accountManagement->createAccount($customer, 'password');
$builder->setCustomAttribute('is_object_new', true);
$repository->save($customer);
