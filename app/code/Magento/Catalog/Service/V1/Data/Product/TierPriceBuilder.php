<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Product;

use Magento\Framework\Service\Data\AbstractObjectBuilder;

class TierPriceBuilder extends AbstractObjectBuilder
{
    /**
     * Set Quantity
     *
     * @param float $qty
     * @return $this
     */
    public function setQty($qty)
    {
        $this->_set(TierPrice::QTY, $qty);
        return $this;
    }

    /**
     * Set Value
     *
     * @param double $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->_set(TierPrice::VALUE, $value);
        return $this;
    }
}
