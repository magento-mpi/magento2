<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var \Magento\Customer\Service\V1\CustomerGroupService $customerGroupService */
$customerGroupService = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Customer\Service\V1\CustomerGroupService');
$customerGroup = new Magento\Customer\Service\V1\Dto\CustomerGroup([
    'code'          => 'custom_group',
    'tax_class_id'  => 3,
]);
$customerGroupService->saveGroup($customerGroup);
