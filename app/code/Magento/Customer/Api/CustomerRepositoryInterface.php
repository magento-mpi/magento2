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
interface CustomerRepositoryInterface
{
    /**
     * Create customer.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\InputException If bad input is provided
     * @throws \Magento\Framework\Exception\State\InputMismatchException If the provided email is already used
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magento\Customer\Api\Data\CustomerInterface $customer);

    /**
     * Retrieve customer.
     *
     * @param \Magento\Customer\Api\Data\IdentityInterface $customerId
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer with the specified ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get(\Magento\Customer\Api\Data\IdentityInterface $customerId);

    /**
     * Retrieve customers which match a specified criteria.
     *
     * @param \Magento\Framework\Api\Data\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Customer\Api\Data\CustomerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\Data\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete customer.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magento\Customer\Api\Data\CustomerInterface $customer);

    /**
     * Delete customer by ID.
     *
     * @param int $customerId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($customerId);
}
