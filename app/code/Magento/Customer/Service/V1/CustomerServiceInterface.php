<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

use Magento\Exception\InputException;
use Magento\Exception\NoSuchEntityException;

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
     * @throws \Magento\Customer\Exception If something goes wrong during save
     * @throws InputException If bad input is provided
     * @return int customer ID
     */
    public function saveCustomer(Dto\Customer $customer, $password = null);

    /**
     * Retrieve Customer
     *
     * @param int $customerId
     * @throws NoSuchEntityException If customer with customerId is not found.
     * @return Dto\Customer
     */
    public function getCustomer($customerId);

    /**
     * Delete Customer
     *
     * @param int $customerId
     *
     * @return void
     * @throws NoSuchEntityException If customer with customerId is not found.
     */
    public function deleteCustomer($customerId);
}
