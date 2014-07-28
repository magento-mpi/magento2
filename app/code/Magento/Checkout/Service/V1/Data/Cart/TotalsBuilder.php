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
    public function setGrandTotal($value)
    {
        return $this->_set(Totals::GRAND_TOTAL, $value);
    }

    /**
     * Set grand total in base currency
     *
     * @param double|null $value
     * @return $this
     */
    public function setBaseGrandTotal($value)
    {
        return $this->_set(Totals::BASE_GRAND_TOTAL, $value);
    }

    /**
     * Set subtotal in quote currency
     *
     * @param double|null $value
     * @return $this
     */
    public function setSubtotal($value)
    {
        return $this->_set(Totals::SUBTOTAL, $value);
    }

    /**
     * Set subtotal in base currency
     *
     * @param double|null $value
     * @return $this
     */
    public function setBaseSubtotal($value)
    {
        return $this->_set(Totals::BASE_SUBTOTAL, $value);
    }

    /**
     * Set subtotal in quote currency with applied discount
     *
     * @param double|null $value
     * @return $this
     */
    public function setSubtotalWithDiscount($value)
    {
        return $this->_set(Totals::SUBTOTAL_WITH_DISCOUNT, $value);
    }

    /**
     * Set subtotal in base currency with applied discount
     *
     * @param double|null $value
     * @return $this
     */
    public function setBaseSubtotalWithDiscount($value)
    {
        return $this->_set(Totals::BASE_SUBTOTAL_WITH_DISCOUNT, $value);
    }
}
