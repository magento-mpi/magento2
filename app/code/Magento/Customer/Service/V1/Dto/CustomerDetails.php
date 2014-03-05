<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto;

/**
 * Class CustomerDetails
 */
class CustomerDetails extends \Magento\Service\Entity\AbstractDto
{
    const KEY_CUSTOMER = 'customer';
    const KEY_ADDRESSES = 'addresses';

    /**
     * Get addresses
     *
     * @return \Magento\Customer\Service\V1\Dto\Address[]|null
     */
    public function getAddresses()
    {
        return $this->_get(self::KEY_ADDRESSES);
    }

    /**
     * Get customer
     *
     * @return \Magento\Customer\Service\V1\Dto\Customer
     */
    public function getCustomer()
    {
        return $this->_get(self::KEY_CUSTOMER);
    }
}
