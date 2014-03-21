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

class Adjustment extends AbstractAdjustment
{
    /**
     * @return string
     */
    public function getAdjustmentCode()
    {
        return 'tax';
    }

    /**
     * @return \Magento\Tax\Pricing\Adjustment
     */
    public function getTaxAdjustment()
    {
        return $this->amountRender->getProduct()->getPriceInfo()->getAdjustment($this->getAdjustmentCode());
    }

    public function displayPriceIncludingTax()
    {
        return $this->priceHelper->displayPriceIncludingTax();
    }

    public function displayBothPrices()
    {
        return $this->priceHelper->displayBothPrices();
    }
}
