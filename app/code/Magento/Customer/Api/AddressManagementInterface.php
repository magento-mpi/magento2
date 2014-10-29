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
 * Interface for managing customers addresses.
 */
interface AddressManagementInterface
{
    /**
     * Validate customer address data.
     *
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @return \Magento\Customer\Api\Data\ValidationResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate(\Magento\Customer\Api\Data\AddressInterface $address);
}
