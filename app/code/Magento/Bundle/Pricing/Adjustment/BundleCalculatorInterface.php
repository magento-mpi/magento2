<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Adjustment;

use Magento\Framework\Pricing\Object\SaleableInterface;
use Magento\Framework\Pricing\Adjustment\CalculatorInterface;

/**
 * Bundle calculator interface
 */
interface BundleCalculatorInterface extends CalculatorInterface
{
    /**
     * @param float|string $amount
     * @param SaleableInterface $saleableItem
     * @param null|bool $exclude
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getMaxAmount($amount, SaleableInterface $saleableItem, $exclude = null);
}
