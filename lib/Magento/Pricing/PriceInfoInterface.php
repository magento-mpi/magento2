<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing;

use Magento\Pricing\Adjustment\AdjustmentInterface;
use Magento\Pricing\Price\PriceInterface;

interface PriceInfoInterface
{
    /**
     * @return PriceInterface[]
     */
    public function getPrices();

    /**
     * @param string $priceCode
     * @return PriceInterface
     */
    public function getPrice($priceCode);

    /**
     * @return AdjustmentInterface[]
     */
    public function getAdjustments();

    /**
     * @param string $adjustmentCode
     * @return AdjustmentInterface
     */
    public function getAdjustment($adjustmentCode);
}
