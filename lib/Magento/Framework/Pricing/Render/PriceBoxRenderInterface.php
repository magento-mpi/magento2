<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Pricing\Render;

use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\Object\SaleableInterface;

/**
 * Price box render interface
 */
interface PriceBoxRenderInterface
{
    /**
     * @return SaleableInterface
     */
    public function getSaleableItem();

    /**
     * Retrieve price object
     * (to use in templates only)
     *
     * @return PriceInterface
     */
    public function getPrice();

    /**
     * Retrieve amount html for given price and arguments
     * (to use in templates only)
     *
     * @param AmountInterface $price
     * @param array $arguments
     * @return string
     */
    public function renderAmount(AmountInterface $price, array $arguments = []);
}
