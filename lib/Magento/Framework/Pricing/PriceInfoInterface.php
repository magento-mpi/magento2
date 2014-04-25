<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Pricing;

use Magento\Framework\Pricing\Adjustment\AdjustmentInterface;
use Magento\Framework\Pricing\Price\PriceInterface;

/**
 * Price info model interface
 */
interface PriceInfoInterface
{
    /**
     * Default product quantity
     */
    const PRODUCT_QUANTITY_DEFAULT = 1.;

    /**
     * @return PriceInterface[]
     */
    public function getPrices();

    /**
     * @param string $priceCode
     * @param float|null $quantity
     * @return PriceInterface
     */
    public function getPrice($priceCode, $quantity = null);

    /**
     * @return AdjustmentInterface[]
     */
    public function getAdjustments();

    /**
     * @param string $adjustmentCode
     * @return AdjustmentInterface
     */
    public function getAdjustment($adjustmentCode);

    /**
     * @return PriceInterface[]
     */
    public function getPricesIncludedInBase();
}
