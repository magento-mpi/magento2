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

/** @var \Magento\Customer\Api\Data\GroupDataBuilder $builder */
$builder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    '\Magento\Customer\Api\Data\GroupDataBuilder'
);
$customerGroupBuilder = $builder->setCode('custom_group')->setTaxClassId(3);

/** @var \Magento\Customer\Api\Data\GroupInterface $customerGroup */
$customerGroup = new \Magento\Customer\Model\Data\Group($customerGroupBuilder);
$groupRepository->save($customerGroup);
