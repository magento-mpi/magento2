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

use Magento\Pricing\Price\PriceInterface;
use Magento\Pricing\Object\SaleableInterface;

/**
 * Price box render interface
 */
interface PriceBoxRenderInterface
{
    /**
     * Retrieve pricebox html for given price type, item and arguments
     *
     * @param string $priceType
     * @param SaleableInterface $saleableItem
     * @param array $arguments
     * @return string
     */
    public function render($priceType, SaleableInterface $saleableItem, array $arguments = []);

    /**
     * Retrieve saleable item object
     * (to use in templates only)
     *
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
     * @param PriceInterface $price
     * @param array $arguments
     * @return string
     */
    public function renderAmount(PriceInterface $price, array $arguments = []);
}
