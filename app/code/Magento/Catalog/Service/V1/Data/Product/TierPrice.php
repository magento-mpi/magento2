<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Product;

use Magento\Framework\Service\Data\AbstractObject;

class TierPrice extends AbstractObject
{
    const QTY = 'qty';

    const VALUE = 'value';

    /**
     * Retrieve tier qty
     *
     * @return float
     */
    public function getQty()
    {
        return $this->_get(self::QTY);
    }

    /**
     * Retrieve price value
     *
     * @return double
     */
    public function getValue()
    {
        return $this->_get(self::VALUE);
    }
}
