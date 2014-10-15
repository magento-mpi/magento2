<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Api;

interface CustomerValidation
{
    /**
     * Validate customer entity
     *
     * @param \Magento\Customer\Api\Data\Customer $customer
     * @param \Magento\Eav\Api\Attribute[] $attributes
     * @return \Magento\Customer\Service\V1\Data\CustomerValidationResults
     */
    public function validate(
        \Magento\Customer\Api\Data\Customer $customer,
        array $attributes = [] //??? can remove this
    );
}
