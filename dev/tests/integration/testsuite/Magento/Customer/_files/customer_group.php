<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var \Magento\Customer\Service\V1\CustomerGroupService $customerGroupService */
$customerGroupService = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Customer\Service\V1\CustomerGroupService'
);

$builder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    '\Magento\Customer\Service\V1\Data\CustomerGroupBuilder'
);
$customerGroupBuilder = $builder->setCode('custom_group')->setTaxClassId(3);

$customerGroup = new Magento\Customer\Service\V1\Data\CustomerGroup($customerGroupBuilder);
$customerGroupService->createGroup($customerGroup);
