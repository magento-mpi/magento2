<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\Data;

use Magento\Framework\Service\Data\AbstractObject as DataObject;

/**
 * Class OrderStatusHistory
 */
class OrderStatusHistory extends DataObject
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
     * int
     */
    const IS_CUSTOMER_NOTIFIED = 'is_customer_notified';

    /**
     * int
     */
    const IS_VISIBLE_ON_FRONT = 'is_visible_on_front';

    /**
     * string
     */
    const COMMENT = 'comment';

    /**
     * string
     */
    const STATUS = 'status';

    /**
     * string
     */
    const CREATED_AT = 'created_at';

    /**
     * string
     */
    const ENTITY_NAME = 'entity_name';

    /**
     * Returns comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->_get(self::COMMENT);
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
     * Returns entity_id
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->_get(self::ENTITY_ID);
    }

    /**
     * Returns entity_name
     *
     * @return string
     */
    public function getEntityName()
    {
        return $this->_get(self::ENTITY_NAME);
    }

    /**
     * Returns is_customer_notified
     *
     * @return int
     */
    public function getIsCustomerNotified()
    {
        return $this->_get(self::IS_CUSTOMER_NOTIFIED);
    }

    /**
     * Returns is_visible_on_front
     *
     * @return int
     */
    public function getIsVisibleOnFront()
    {
        return $this->_get(self::IS_VISIBLE_ON_FRONT);
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
     * Returns status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }
}
