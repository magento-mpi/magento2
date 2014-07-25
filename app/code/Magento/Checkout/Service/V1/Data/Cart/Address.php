<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data\Cart;

/**
 * Quote billing/shipping address data
 *
 * @codeCoverageIgnore
 */
class Address extends \Magento\Customer\Service\V1\Data\Address
{
    const KEY_EMAIL = 'email';

    /**
     * Get billing/shipping email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->_get(self::KEY_EMAIL);
    }

}
