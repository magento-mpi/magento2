<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Data\Cart;

/**
 * Coupon data for quote
 *
 * @codeCoverageIgnore
 */
class Coupon extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    /**
     * Constants defined for keys of array
     */
    const COUPON_CODE = 'coupon_code';

    /**
     * @return string
     */
    public function getCouponCode()
    {
        return $this->_get(self::COUPON_CODE);
    }
}
