<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data\Cart;

/**
 * Quote address data object builder
 *
 * @codeCoverageIgnore
  */
class AddressBuilder extends \Magento\Customer\Service\V1\Data\AddressBuilder
{
    /**
     * @param $value string
     * @return $this
     */
    public function setEmail($value)
    {
        return $this->_set(Address::KEY_EMAIL, $value);
    }
}
