<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Product;

class TierPriceBuilder extends GroupPriceBuilder
{
    /**
     * Set Quantity
     *
     * @param double $qty
     * @return $this
     */
    public function setQty($qty)
    {
        $this->_set(TierPrice::QTY, $qty);
        return $this;
    }
}
