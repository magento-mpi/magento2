<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Api;

/**
 * Customer address CRUD interface.
 */
interface AddressRepositoryInterface
{
    /**
     * Save customer address.
     *
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @return \Magento\Customer\Api\Data\AddressInterface
     */
    public function save(\Magento\Customer\Api\Data\AddressInterface $address);

    /**
     * Retrieve customer address.
     *
     * @param int $addressId
     * @return \Magento\Customer\Api\Data\AddressInterface
     */
    public function get($addressId);

    /**
     * Retrieve customers addresses matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Customer\Api\Data\AddressSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete customer address.
     *
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @return bool true on success
     */
    public function delete(\Magento\Customer\Api\Data\AddressInterface $address);

    /**
     * Delete customer address by ID.
     *
     * @param int $addressId
     * @return bool true on success
     */
    public function deleteById($addressId);
}
