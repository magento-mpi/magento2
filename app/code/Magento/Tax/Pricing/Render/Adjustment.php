<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Pricing\Render;

use Magento\View\Element\Template;
use Magento\Pricing\Render\AbstractAdjustment;

/**
 * @method string getIdSuffix()
 * @method string getDisplayLabel()
 */
class Adjustment extends AbstractAdjustment
{
    /**
     * @return string
     */
    public function getAdjustmentCode()
    {
        //@TODO We can build two model using DI, not code. What about passing it in constructor?
        return \Magento\Tax\Pricing\Adjustment::CODE;
    }

    /**
     * @return bool
     */
    public function displayBothPrices()
    {
        return $this->priceHelper->displayBothPrices();
    }

    /**
     * @return string
     */
    public function getDisplayAmountExclTax()
    {
        return $this->convertAndFormatCurrency($this->getPrice()->getDisplayValue('tax'), false);
    }

    /**
     * @param bool $includeContainer
     * @return string
     */
    public function getDisplayAmount($includeContainer = true)
    {
         return $this->convertAndFormatCurrency($this->getPrice()->getDisplayValue(), $includeContainer);
    }

    /**
     * Build identifier with prefix
     *
     * @param string $prefix
     * @return string
     */
    public function buildIdWithPrefix($prefix)
    {
        $productId = $this->getSaleableItem()->getId();
        return $prefix . $productId . $this->getIdSuffix();
    }
}
