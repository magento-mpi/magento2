<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto;

class CustomerDetails extends \Magento\Service\Entity\AbstractDto
{
    const KEY_CUSTOMER = 'customer';
    const KEY_ADDRESSES = 'addresses';

    /**
     * @return Address[]|null
     */
    public function getAddresses()
    {
        return $this->_get(self::KEY_ADDRESSES);
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->_get(self::KEY_CUSTOMER);
    }
}
