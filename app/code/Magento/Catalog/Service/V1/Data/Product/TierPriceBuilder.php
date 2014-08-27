<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Product;

use Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder;

/**
 * @codeCoverageIgnore
 */
class TierPriceBuilder extends AbstractExtensibleObjectBuilder
{
    /**
     * Set Quantity
     *
     * @param float $qty
     * @return $this
     */
    public function setQty($qty)
    {
        return $this->_set(TierPrice::QTY, $qty);
    }

    /**
     * Set Value
     *
     * @param float $value
     * @return $this
     */
    public function setValue($value)
    {
        return $this->_set(TierPrice::VALUE, $value);
    }
}
