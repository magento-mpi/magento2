<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\Render;

use Magento\Pricing\Amount\AmountInterface;
use Magento\Pricing\Price\PriceInterface;
use Magento\Pricing\Object\SaleableInterface;

/**
 * Price amount renderer interface
 */
interface AmountRenderInterface
{
    /**
     * Enforce custom display price value
     *
     * @param float $value
     * @return void
     */
    public function setDisplayValue($value);

    /**
     * @return float
     */
    public function getDisplayValue();

    /**
     * Retrieve amount object
     *
     * @return AmountInterface
     */
    public function getAmount();

    /**
     * @return SaleableInterface
     */
    public function getSaleableItem();

    /**
     * @return PriceInterface
     */
    public function getPrice();

    /**
     * @return string
     */
    public function getAdjustmentsHtml();
}
