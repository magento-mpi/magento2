<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Data\Product;

use \Magento\Framework\Api\AbstractExtensibleObject;

/**
 * @codeCoverageIgnore
 */
class Link extends AbstractExtensibleObject
{
    const SKU = 'sku';

    const OPTION_ID = 'option_id';

    const QTY = 'qty';

    const CAN_CHANGE_QUANTITY = 'can_change_qty';

    const POSITION = 'position';

    const DEFINED = 'defined';

    const IS_DEFAULT = 'default';

    const PRICE = 'price';

    const PRICE_TYPE = 'price_type';

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
    public function getOptionId()
    {
        return $this->_get(self::OPTION_ID);
    }

    /**
     * @return float|null
     */
    public function getQty()
    {
        return $this->_get(self::QTY);
    }

    /**
     * @return int|null
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }

    /**
     * @return bool|null
     */
    public function isDefined()
    {
        return (bool)$this->_get(self::DEFINED);
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return (bool)$this->_get(self::IS_DEFAULT);
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->_get(self::PRICE);
    }

    /**
     * @return int
     */
    public function getPriceType()
    {
        return $this->_get(self::PRICE_TYPE);
    }

    /**
     * Get whether quantity could be changed
     *
     * @return int|null
     */
    public function getCanChangeQuantity()
    {
        return $this->_get(self::CAN_CHANGE_QUANTITY);
    }
}
