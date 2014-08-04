<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data\Cart;

/**
 * Cart Totals
 *
 * @codeCoverageIgnore
 */
class Totals extends \Magento\Framework\Service\Data\AbstractObject
{
    const GRAND_TOTAL = 'grand_total';

    const BASE_GRAND_TOTAL = 'base_grand_total';

    const SUBTOTAL = 'subtotal';

    const BASE_SUBTOTAL = 'base_subtotal';

    const SUBTOTAL_WITH_DISCOUNT = 'subtotal_with_discount';

    const BASE_SUBTOTAL_WITH_DISCOUNT = 'base_subtotal_with_discount';

    /**
     * Get grand total in quote currency
     *
     * @return float|null
     */
    public function getGrandTotal()
    {
        return $this->_get(self::GRAND_TOTAL);
    }

    /**
     * Get grand total in base currency
     *
     * @return float|null
     */
    public function getBaseGrandTotal()
    {
        return $this->_get(self::BASE_GRAND_TOTAL);
    }

    /**
     * Get subtotal in quote currency
     *
     * @return float|null
     */
    public function getSubtotal()
    {
        return $this->_get(self::SUBTOTAL);
    }

    /**
     * Get subtotal in base currency
     *
     * @return float|null
     */
    public function getBaseSubtotal()
    {
        return $this->_get(self::BASE_SUBTOTAL);
    }

    /**
     * Get subtotal in quote currency with applied discount
     *
     * @return float|null
     */
    public function getSubtotalWithDiscount()
    {
        return $this->_get(self::SUBTOTAL_WITH_DISCOUNT);
    }

    /**
     * Get subtotal in base currency with applied discount
     *
     * @return float|null
     */
    public function getBaseSubtotalWithDiscount()
    {
        return $this->_get(self::BASE_SUBTOTAL_WITH_DISCOUNT);
    }
}
