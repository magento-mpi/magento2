<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product;


class TierPrice extends \Magento\Framework\Model\AbstractExtensibleModel
    implements \Magento\Catalog\Api\Data\ProductTierPriceInterface
{
    /**
     * Retrieve tier qty
     *
     * @return float
     */
    public function getQty()
    {
        return $this->getData(self::QTY);
    }

    /**
     * Retrieve price value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->getData(self::VALUE);
    }
}
