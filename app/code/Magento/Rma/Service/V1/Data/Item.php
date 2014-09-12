<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Service\V1\Data;

class Item extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    /**#@+
     * Constants defined for keys of array
     */
    const ORDER_ITEM_ID = 'order_item_id';

    const QTY_REQUESTED = 'qty_requested';

    const QTY_AUTHORIZED = 'qty_authorized';

    const QTY_RETURNED = 'qty_returned';

    const QTY_APPROVED = 'qty_approved';

    const REASON = 'reason';

    const CONDITION = 'condition';

    const RESOLUTION = 'resolution';

    const STATUS = 'status';

    /**#@-*/

    /**
     * Get order_item_id
     *
     * @return int
     */
    public function getOrderItemId()
    {
        return $this->_get(self::ORDER_ITEM_ID);
    }


    /**
     * Get qty_requested
     *
     * @return int
     */
    public function getQrtRequested()
    {
        return $this->_get(self::QTY_REQUESTED);
    }


    /**
     * Get reason
     *
     * @return string
     */
    public function getReason()
    {
        return $this->_get(self::REASON);
    }


    /**
     * Get condition
     *
     * @return string
     */
    public function getCondition()
    {
        return $this->_get(self::CONDITION);
    }


    /**
     * Get resolution
     *
     * @return string
     */
    public function getResolution()
    {
        return $this->_get(self::RESOLUTION);
    }


    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }
}
