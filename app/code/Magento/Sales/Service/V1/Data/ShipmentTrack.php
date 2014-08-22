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
 * Class ShipmentTrack
 */
class ShipmentTrack extends DataObject
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
    const WEIGHT = 'weight';

    /**
     * float
     */
    const QTY = 'qty';

    /**
     * int
     */
    const ORDER_ID = 'order_id';

    /**
     * string
     */
    const TRACK_NUMBER = 'track_number';

    /**
     * string
     */
    const DESCRIPTION = 'description';

    /**
     * string
     */
    const TITLE = 'title';

    /**
     * string
     */
    const CARRIER_CODE = 'carrier_code';

    /**
     * string
     */
    const CREATED_AT = 'created_at';

    /**
     * string
     */
    const UPDATED_AT = 'updated_at';

    /**
     * Returns carrier_code
     *
     * @return string
     */
    public function getCarrierCode()
    {
        return $this->_get(self::CARRIER_CODE);
    }

    /**
     * Returns created_at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
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
     * Returns order_id
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->_get(self::ORDER_ID);
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
     * Returns qty
     *
     * @return float
     */
    public function getQty()
    {
        return $this->_get(self::QTY);
    }

    /**
     * Returns title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }

    /**
     * Returns track_number
     *
     * @return string
     */
    public function getTrackNumber()
    {
        return $this->_get(self::TRACK_NUMBER);
    }

    /**
     * Returns updated_at
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->_get(self::UPDATED_AT);
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
