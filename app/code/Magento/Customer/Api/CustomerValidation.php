<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Api;

/**
 * Interface for customer data validation.
 */
interface CustomerValidation
{
    /**
     * Validate customer data.
     *
     * @param \Magento\Customer\Api\Data\Customer $customer
     * @return \Magento\Customer\Service\V1\Data\CustomerValidationResults
     */
    public function validate(\Magento\Customer\Api\Data\Customer $customer);
}
