<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Data;

use Magento\Framework\Service\Data\AbstractExtensibleObject as DataObject;

/**
 * Class ShipmentItem
 */
class ShipmentItem extends DataObject
{

    /**
     * int
     */
    const ENTITY_ID = 'entity_id';

    /**
     * int
     */
    const PARENT_ID = 'parent_id';

    /**
     * float
     */
    const ROW_TOTAL = 'row_total';

    /**
     * float
     */
    const PRICE = 'price';

    /**
     * float
     */
    const WEIGHT = 'weight';

    /**
     * float
     */
    const QTY = 'qty';

    /**
     * int
     */
    const PRODUCT_ID = 'product_id';

    /**
     * int
     */
    const ORDER_ITEM_ID = 'order_item_id';

    /**
     * string
     */
    const ADDITIONAL_DATA = 'additional_data';

    /**
     * string
     */
    const DESCRIPTION = 'description';

    /**
     * string
     */
    const NAME = 'name';

    /**
     * string
     */
    const SKU = 'sku';

    /**
     * Returns additional_data
     *
     * @return string
     */
    public function getAdditionalData()
    {
        return $this->_get(self::ADDITIONAL_DATA);
    }

    /**
     * Returns description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * Returns entity_id
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->_get(self::ENTITY_ID);
    }

    /**
     * Returns name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Returns order_item_id
     *
     * @return int
     */
    public function getOrderItemId()
    {
        return $this->_get(self::ORDER_ITEM_ID);
    }

    /**
     * Returns parent_id
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->_get(self::PARENT_ID);
    }

    /**
     * Returns price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->_get(self::PRICE);
    }

    /**
     * Returns product_id
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->_get(self::PRODUCT_ID);
    }

    /**
     * Returns qty
     *
     * @return float
     */
    public function getQty()
    {
        return $this->_get(self::QTY);
    }

    /**
     * Returns row_total
     *
     * @return float
     */
    public function getRowTotal()
    {
        return $this->_get(self::ROW_TOTAL);
    }

    /**
     * Returns sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->_get(self::SKU);
    }

    /**
     * Returns weight
     *
     * @return float
     */
    public function getWeight()
    {
        return $this->_get(self::WEIGHT);
    }
}
