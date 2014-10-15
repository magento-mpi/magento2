<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Api;

interface CustomerRepository
{
    /**
     * Create Customer Account
     *
     * @param \Magento\Customer\Api\Data\Customer
     * @return \Magento\Customer\Api\Data\Customer
     * @throws \Magento\Framework\Exception\InputException If bad input is provided
     * @throws \Magento\Framework\Exception\State\InputMismatchException If the provided email is already used
     */
    public function persist(\Magento\Customer\Api\Data\Customer $customer);

    /**
     * @param \Magento\Customer\Api\Data\Customer $customer
     * @return \Magento\Customer\Api\Data\Customer
     */
    public function update(\Magento\Customer\Api\Data\Customer $customer);

    /**
     * Retrieve Customer
     *
     * @param string $email
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer with customerId is not found.
     * @return \Magento\Customer\Api\Data\Customer
     */
    public function get($email);

    /**
     * Retrieve customers which match a specified criteria
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return mixed
     */
    public function getList(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);

    /**
     * Delete Customer
     *
     * @param string $email
     * @throws \Magento\Customer\Exception If something goes wrong during delete
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer with customerId is not found.
     * @return bool True if the customer was deleted
     */
    public function remove($email);
}
