<?php
/**
 * CustomerDatails class
 *
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
     * @return Address[]
     */
    public function getAddresses()
    {
        return $this->_get(self::KEY_ADDRESSES);
    }

    /**
     * @return Customer|null
     */
    public function getCustomer()
    {
        return $this->_get(self::KEY_CUSTOMER);
    }
}
