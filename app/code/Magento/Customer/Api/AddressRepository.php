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
     * @return \Magento\Customer\Api\Data\Address
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magento\Customer\Api\Data\Address $address);

    /**
     * Retrieve customer address.
     *
     * @param int $addressId
     * @return \Magento\Customer\Api\Data\Address
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($addressId);

    /**
     * Retrieve customers addresses matching the specified criteria.
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Customer\Api\Data\AddressSearchResults
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);

    /**
     * Delete customer address.
     *
     * @param int $addressId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete($addressId);
}
