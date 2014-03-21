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

interface PriceBoxRenderInterface
{
    /**
     * @param string $priceType
     * @param SaleableInterface $saleableItem
     * @param array $arguments
     * @return string
     */
    public function render($priceType, SaleableInterface $saleableItem, array $arguments = []);

    /**
     * (to use in templates only)
     *
     * @return SaleableInterface
     */
    public function getSaleableItem();

    /**
     * (to use in templates only)
     *
     * @return PriceInterface
     */
    public function getPrice();

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
