<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Render;

use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;

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
        if ($renderBlock && $renderBlock->getItem() instanceof ItemInterface) {
            $price->setItem($renderBlock->getItem());
        } elseif ($renderBlock
            && $renderBlock->getParentBlock()
            && $renderBlock->getParentBlock()->getItem() instanceof ItemInterface
        ) {
            $price->setItem($renderBlock->getParentBlock()->getItem());
        }
        return parent::_prepareLayout();
    }
}
