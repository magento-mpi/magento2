<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Service\V1\Data;

use Magento\Framework\Api\AbstractExtensibleObject as DataObject;

/**
 * Class RmaStatusHistory
 */
class RmaStatusHistory extends DataObject
{
    /**#@+
     * Data object properties
     */
    const ENTITY_ID = 'entity_id';
    const CUSTOMER_NOTIFIED = 'customer_notified';
    const VISIBLE_ON_FRONT = 'visible_on_front';
    const COMMENT = 'comment';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const ADMIN = 'admin';

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
     * Returns is_customer_notified
     *
     * @return bool
     */
    public function isCustomerNotified()
    {
        return $this->_get(self::CUSTOMER_NOTIFIED);
    }

    /**
     * Returns is_visible_on_front
     *
     * @return bool
     */
    public function isVisibleOnFront()
    {
        return $this->_get(self::VISIBLE_ON_FRONT);
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

    /**
     * Returns is_admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->_get(self::ADMIN);
    }
}
