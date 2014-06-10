<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Product;

class TierPrice extends GroupPrice
{
    const QTY = 'qty';

    /**
     * Retrieve tier qty
     *
     * @return float
     */
    public function getQty()
    {
        return $this->_get(self::QTY);
    }
}
