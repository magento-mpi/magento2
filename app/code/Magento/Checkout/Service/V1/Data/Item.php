<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Data;

/**
 * @codeCoverageIgnore
 */
class Item extends \Magento\Framework\Service\Data\AbstractObject
{
    /**#@+
     * Constants defined for keys of array
     */
    const SKU = 'sku';

    const QTY = 'qty';

    const NAME = 'name';

    const PRICE = 'price';

    const TYPE = 'type';

    /**
     * @return string|null
     */
    public function getSku()
    {
        return $this->_get(self::SKU);
    }

    /**
     * @return int|null
     */
    public function getQty()
    {
        return $this->_get(self::QTY);
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * @return float|null
     */
    public function getPrice()
    {
        return $this->_get(self::PRICE);
    }

    /**
     * @return string|null
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }
}
