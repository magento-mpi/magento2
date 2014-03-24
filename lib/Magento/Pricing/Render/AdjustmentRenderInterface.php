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
use \Magento\View\Element\Template;

interface AdjustmentRenderInterface
{
    /**
     * @param string $html
     * @param AmountRenderInterface $amountRender
     * @param array $arguments
     * @return string
     */
    public function render($html, AmountRenderInterface $amountRender, array $arguments = []);

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
     * @return array
     */
    public function getData();

    /**
     * (to use in templates only)
     *
     * @return PriceInterface
     */
    public function getPrice();

    /**
     * (to use in templates only)
     *
     * @return SaleableInterface
     */
    public function getSaleableItem();

    /**
     * (to use in templates only)
     *
     * @return \Magento\Pricing\Adjustment\AdjustmentInterface
     */
    public function getAdjustment();
}
