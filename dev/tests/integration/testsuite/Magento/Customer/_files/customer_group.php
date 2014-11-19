<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var \Magento\Customer\Api\GroupRepositoryInterface $groupRepository */
$groupRepository = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Customer\Api\GroupRepositoryInterface'
);

$groupBuilder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Customer\Api\Data\GroupInterfaceBuilder'
);
$groupBuilder->setCode('custom_group')->setTaxClassId(5);
$groupRepository->save($groupBuilder->create());
