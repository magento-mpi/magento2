<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Data\Cart;

/**
 * @codeCoverageIgnore
 */
class CouponBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * @param string $value
     * @return $this
     */
    public function setCouponCode($value)
    {
        $this->_set(Coupon::COUPON_CODE, $value);
        return $this;
    }
}
