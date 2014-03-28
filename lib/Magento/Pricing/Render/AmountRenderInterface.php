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
     * Retrieve amount html for given price, item and arguments
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
}
