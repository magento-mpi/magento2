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
 * Price amount renderer interface
 */
interface AmountRenderInterface
{
    /**
     * @param float $amount
     * @param PriceInterface $price
     * @param SaleableInterface $product
     * @param array $arguments
     * @return string
     */
    public function render($amount, PriceInterface $price, SaleableInterface $product, array $arguments = []);

    /**
     * (to use in templates only)
     *
     * @return SaleableInterface
     */
    public function getProduct();

    /**
     * (to use in templates only)
     *
     * @param float $amount
     * @return float
     */
    public function convertToDisplayCurrency($amount);

    /**
     * (to use in templates only)
     *
     * @return string
     */
    public function getDisplayCurrencySymbol();
}
