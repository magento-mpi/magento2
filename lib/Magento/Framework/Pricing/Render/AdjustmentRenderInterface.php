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

use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\Object\SaleableInterface;

/**
 * Adjustment render interface
 */
interface AdjustmentRenderInterface
{
    /**
     * @param AmountRenderInterface $amountRender
     * @param array $arguments
     * @return string
     */
    public function render(AmountRenderInterface $amountRender, array $arguments = []);

    /**
     * @return string
     */
    public function getAdjustmentCode();

    /**
     * @return array
     */
    public function getData();

    /**
     * (to use in templates only)
     *
     * @return AmountRenderInterface
     */
    public function getAmountRender();

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
     * @return \Magento\Framework\Pricing\Adjustment\AdjustmentInterface
     */
    public function getAdjustment();
}
