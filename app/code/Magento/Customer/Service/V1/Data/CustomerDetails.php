<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data;

/**
 * Class CustomerDetails
 */
class CustomerDetails extends \Magento\Framework\Api\AbstractExtensibleObject
{
    const KEY_CUSTOMER = 'customer';

    const KEY_ADDRESSES = 'addresses';

    /**
     * Get addresses
     *
     * @return \Magento\Customer\Service\V1\Data\Address[]|null
     */
    public function getAddresses()
    {
        return $this->_get(self::KEY_ADDRESSES);
    }

    /**
     * Get customer
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomer()
    {
        return $this->_get(self::KEY_CUSTOMER);
    }
}
