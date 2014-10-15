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
 * Customer address CRUD interface.
 */
interface AddressRepository
{
    /**
     * Save customer address.
     *
     * @param \Magento\Customer\Api\Data\Address $address
     * @param int $customerId
     * @return \Magento\Customer\Api\Data\Address
     */
    public function persist(\Magento\Customer\Api\Data\Address $address, $customerId);

    /**
     * Retrieve customer address.
     *
     * @param int $addressId
     * @return \Magento\Customer\Api\Data\Address
     */
    public function get($addressId);

    /**
     * Retrieve customers addresses matching the specified criteria.
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Customer\Api\Data\Address[]
     */
    public function getList(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);

    /**
     * Delete customer address.
     *
     * @param int $addressId
     * @return int
     */
    public function delete($addressId);
}
