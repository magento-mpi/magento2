<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data\Cart;

/**
 * Cart Totals Builder
 *
 * @codeCoverageIgnore
 */
class TotalsBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set grand total in quote currency
     *
     * @param double|null $value
     * @return $this
     */
    public function getGrandTotal($value)
    {
        return $this->_set(self::GRAND_TOTAL, $value);
    }

    /**
     * Set grand total in base currency
     *
     * @param double|null $value
     * @return $this
     */
    public function getBaseGrandTotal($value)
    {
        return $this->_set(self::BASE_GRAND_TOTAL, $value);
    }

    /**
     * Set subtotal in quote currency
     *
     * @param double|null $value
     * @return $this
     */
    public function getSubtotal($value)
    {
        return $this->_set(self::SUBTOTAL, $value);
    }

    /**
     * Set subtotal in base currency
     *
     * @param double|null $value
     * @return $this
     */
    public function getBaseSubtotal($value)
    {
        return $this->_set(self::BASE_SUBTOTAL, $value);
    }

    /**
     * Set subtotal in quote currency with applied discount
     *
     * @param double|null $value
     * @return $this
     */
    public function getSubtotalWithDiscount($value)
    {
        return $this->_set(self::SUBTOTAL_WITH_DISCOUNT, $value);
    }

    /**
     * Set subtotal in base currency with applied discount
     *
     * @param double|null $value
     * @return $this
     */
    public function getBaseSubtotalWithDiscount($value)
    {
        return $this->_set(self::BASE_SUBTOTAL_WITH_DISCOUNT, $value);
    }
}
