<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

/**
 * Manipulate Customer Address Entities *
 */
interface CustomerServiceInterface
{
    /**
     * Create or update customer information
     *
     * @param Dto\Customer $customer
     * @param string $password
     * @throws \Magento\Customer\Service\Entity\V1\Exception
     * @return int customer ID
     */
    public function saveCustomer(Dto\Customer $customer, $password = null);

    /**
     * Retrieve Customer
     *
     * @param int $customerId
     * @return Dto\Customer
     */
    public function getCustomer($customerId);

}
