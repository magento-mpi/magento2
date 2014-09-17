<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Service\V1\Data;

class Rma extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    /**#@+
     * Constants defined for keys of array
     */
    const ENTITY_ID = 'entity_id';

    const ORDER_ID = 'order_id';

    const ORDER_INCREMENT_ID = 'order_increment_id';

    const INCREMENT_ID = 'increment_id';

    const STORE_ID = 'store_id';

    const CUSTOMER_ID = 'customer_id';

    const DATE_REQUESTED = 'date_requested';

    const ORDER_DATE = 'order_date';

    const CUSTOMER_CUSTOM_EMAIL = 'customer_custom_email';

    const ITEMS = 'items';

    const STATUS = 'status';

    const COMMENTS = 'comments';

    const TRACKS = 'tracks';

    /**#@-*/

    /**
     * Get entity_id
     *
     * @return string
     */
    public function getIncrementId()
    {
        return $this->_get(self::INCREMENT_ID);
    }

    /**
     * Get entity_id
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->_get(self::ENTITY_ID);
    }

    /**
     * Get order_id
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->_get(self::ORDER_ID);
    }

    /**
     * Get order_increment_id
     *
     * @return string
     */
    public function getOrderIncrementId()
    {
        return $this->_get(self::ORDER_INCREMENT_ID);
    }

    /**
     * Get store_id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * Get cutomer_id
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * Get date_requested
     *
     * @return string
     */
    public function getDateRequested()
    {
        return $this->_get(self::DATE_REQUESTED);
    }

    /**
     * Get order_date
     *
     * @return string
     */
    public function getOrderDate()
    {
        return $this->_get(self::ORDER_DATE);
    }

    /**
     * Get customer_custom_email
     *
     * @return string
     */
    public function getCustomerCustomEmail()
    {
        return $this->_get(self::CUSTOMER_CUSTOM_EMAIL);
    }

    /**
     * Get items
     *
     * @return \Magento\Rma\Service\V1\Data\Item[]
     */
    public function getItems()
    {
        return $this->_get(self::ITEMS);
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

    /**
     * Get comments list
     *
     * @return \Magento\Rma\Service\V1\Data\RmaStatusHistory[]
     */
    public function getComments()
    {
        return $this->_get(self::COMMENTS);
    }

    /**
     * Get tracks list
     *
     * @return \Magento\Rma\Service\V1\Data\Track[]
     */
    public function getTracks()
    {
        return $this->_get(self::TRACKS);
    }
}
