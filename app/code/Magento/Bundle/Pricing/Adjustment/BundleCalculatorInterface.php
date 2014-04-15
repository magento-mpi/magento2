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

use Magento\Catalog\Model\Product;
use Magento\Pricing\Adjustment\CalculatorInterface;

/**
 * Bundle calculator interface
 */
interface BundleCalculatorInterface extends CalculatorInterface
{
    /**
     * @param float|string $amount
     * @param Product $saleableItem
     * @param null|bool $exclude
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getMaxAmount($amount, Product $saleableItem, $exclude = null);

    /**
     * Option amount calculation for saleable item
     *
     * @param Product $saleableItem
     * @param null|string $exclude
     * @param bool $searchMin
     * @param \Magento\Pricing\Amount\AmountInterface|null $bundleProductAmount
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getOptionsAmount(
        Product $saleableItem,
        $exclude = null,
        $searchMin = true,
        $bundleProductAmount = null
    );
}
