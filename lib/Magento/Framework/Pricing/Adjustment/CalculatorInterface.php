<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Pricing\Adjustment;

use Magento\Framework\Pricing\Object\SaleableInterface;

/**
 * Calculator interface
 */
interface CalculatorInterface
{
    /**
     * @param float|string $amount
     * @param SaleableInterface $saleableItem
     * @param null|bool|string $exclude
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getAmount($amount, SaleableInterface $saleableItem, $exclude = null);
}
