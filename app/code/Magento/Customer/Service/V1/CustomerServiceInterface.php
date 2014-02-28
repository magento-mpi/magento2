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
     * Retrieve customer by his email.
     *
     * @param string $customerEmail
     * @param int|null $websiteId
     * @throws NoSuchEntityException If customer with the specified email is not found.
     * @return Dto\Customer
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
