<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Render;

/**
 * Class for configured_price rendering
 */
class ConfiguredPriceBox extends FinalPriceBox
{
    /**
     * Retrieve an item instance to the configured price model
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        /** @var $price \Magento\Bundle\Pricing\Price\ConfiguredPrice */
        $price = $this->getPrice();
        /** @var $renderBlock \Magento\Catalog\Pricing\Render */
        $renderBlock = $this->getRenderBlock();
        if ($renderBlock->getParentBlock() && $renderBlock->getParentBlock()->getItem()) {
            $price->setItem($renderBlock->getParentBlock()->getItem());
        }
        return parent::_prepareLayout();
    }
}
