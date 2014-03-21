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

interface AdjustmentRenderInterface
{
    /**
     * @param string $result html given to process by renderer
     * @param PriceInterface $price
     * @param SaleableInterface $product
     * @param array $arguments
     * @return string
     */
    public function render($result, PriceInterface $price, SaleableInterface $product, array $arguments = []);

    /**
     * @return string
     */
    public function getAdjustmentCode();

    /**
     * (to use in templates only)
     *
     * @return string
     */
    public function getOriginalPriceHtml();

    /**
     * (to use in templates only)
     *
     * @return float
     */
    public function getPrice();

    /**
     * (to use in templates only)
     *
     * @return SaleableInterface
     */
    public function getProduct();
}
