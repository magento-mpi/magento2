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
 * Customer CRUD interface.
 */
interface CustomerRepository
{
    /**
     * Create customer.
     *
     * @param \Magento\Customer\Api\Data\Customer $customer
     * @return \Magento\Customer\Api\Data\Customer
     * @throws \Magento\Framework\Exception\InputException If bad input is provided
     * @throws \Magento\Framework\Exception\State\InputMismatchException If the provided email is already used
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magento\Customer\Api\Data\Customer $customer);

    /**
     * Retrieve customer.
     *
     * @param int $customerId
     * @return \Magento\Customer\Api\Data\Customer
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer with the specified ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($customerId);

    /**
     * Retrieve customers which match a specified criteria.
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Customer\Api\Data\Customer[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);

    /**
     * Delete customer.
     *
     * @param int $customerId
     * @return bool True if the customer was deleted
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer with customerId is not found.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete($customerId);
}
