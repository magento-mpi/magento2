<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Adjustment;

use Magento\Pricing\Object\SaleableInterface;
use Magento\Pricing\Adjustment\CalculatorInterface;

/**
 * Bundle calculator interface
 */
interface BundleCalculatorInterface extends CalculatorInterface
{
    /**
     * @param float|string $amount
     * @param SaleableInterface $saleableItem
     * @param null|bool $exclude
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getMaxAmount($amount, SaleableInterface $saleableItem, $exclude = null);

    /**
     * Option amount calculation for saleable item
     *
     * @param SaleableInterface $saleableItem
     * @param null|string $exclude
     * @param bool $searchMin
     * @param \Magento\Pricing\Amount\AmountInterface|null $bundleProductAmount
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getOptionsAmount(
        SaleableInterface $saleableItem,
        $exclude = null,
        $searchMin = true,
        $bundleProductAmount = null
    );
}
