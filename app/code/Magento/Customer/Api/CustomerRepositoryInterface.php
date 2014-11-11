<?php
/**
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
     * @param string $passwordHash
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function save(\Magento\Customer\Api\Data\CustomerInterface $customer, $passwordHash = null);

    /**
     * Retrieve customer.
     *
     * @param string $email
     * @param int|null $websiteId
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer with the specified email does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($email, $websiteId = null);

    /**
     * Retrieve customer.
     *
     * @param int $customerId
     * @param int|null $websiteId
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer with the specified ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($customerId, $websiteId = null);

    /**
     * Retrieve customers which match a specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Customer\Api\Data\CustomerSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete customer.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return bool true on success
     */
    public function delete(\Magento\Customer\Api\Data\CustomerInterface $customer);

    /**
     * Delete customer by ID.
     *
     * @param int $customerId
     * @return bool true on success
     */
    public function deleteById($customerId);
}
