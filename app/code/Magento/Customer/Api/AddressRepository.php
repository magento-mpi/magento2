<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Api;

interface AddressRepository
{
    /**
     * @param int $addressId
     * @param int $customerId
     * @return \Magento\Customer\Api\Data\Address
     */
    public function get($addressId, $customerId);

    /**
     * @param int $customerId
     * @return \Magento\Customer\Api\Data\Address[]
     */
    public function getList($customerId);

    /**
     * @param int $addressId
     * @param int $customerId
     * @return int
     */
    public function delete($addressId, $customerId);

    /**
     * @param \Magento\Customer\Api\Data\Address $address
     * @param int $customerId
     * @return int
     */
    public function persist(\Magento\Customer\Api\Data\Address $address, $customerId);
}
