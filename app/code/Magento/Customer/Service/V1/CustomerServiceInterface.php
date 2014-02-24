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
     * @param Data\Customer $customer
     * @param string $password
     * @throws \Magento\Customer\Exception If something goes wrong during save
     * @throws InputException If bad input is provided
     * @return int customer ID
     */
    public function saveCustomer(Data\Customer $customer, $password = null);

    /**
     * Retrieve Customer
     *
     * @param int $customerId
     * @throws NoSuchEntityException If customer with customerId is not found.
     * @return Data\Customer
     */
    public function getCustomer($customerId);

    /**
     * Retrieve customer by his email.
     *
     * @param string $customerEmail
     * @param int|null $websiteId
     * @throws NoSuchEntityException If customer with the specified email is not found.
     * @return Data\Customer
     */
    public function getCustomerByEmail($customerEmail, $websiteId = null);

    /**
     * Delete Customer
     *
     * @param int $customerId
     * @throws \Magento\Customer\Exception If something goes wrong during delete
     * @throws NoSuchEntityException If customer with customerId is not found.
     * @return void
     */
    public function deleteCustomer($customerId);
}
