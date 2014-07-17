<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Data\Order;

use Magento\Framework\Service\Data\AbstractObject as DataObject;

/**
 * Class Shipment
 */
class Shipment extends DataObject
{
    /**
     * int
     */
    const ENTITY_ID = 'entity_id';

    /**
     * int
     */
    const STORE_ID = 'store_id';

    /**
     * float
     */
    const TOTAL_WEIGHT = 'total_weight';

    /**
     * float
     */
    const TOTAL_QTY = 'total_qty';

    /**
     * int
     */
    const EMAIL_SENT = 'email_sent';

    /**
     * int
     */
    const ORDER_ID = 'order_id';

    /**
     * int
     */
    const CUSTOMER_ID = 'customer_id';

    /**
     * int
     */
    const SHIPPING_ADDRESS_ID = 'shipping_address_id';

    /**
     * int
     */
    const BILLING_ADDRESS_ID = 'billing_address_id';

    /**
     * int
     */
    const SHIPMENT_STATUS = 'shipment_status';

    /**
     * string
     */
    const INCREMENT_ID = 'increment_id';

    /**
     * string
     */
    const CREATED_AT = 'created_at';

    /**
     * string
     */
    const UPDATED_AT = 'updated_at';

    /**
     * string
     */
    const PACKAGES = 'packages';

    /**
     * mediumblob
     */
    const SHIPPING_LABEL = 'shipping_label';

    /**
     * Returns billing_address_id
     *
     * @return int
     */
    public function getBillingAddressId()
    {
        return $this->_get(self::BILLING_ADDRESS_ID);
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
     * Returns customer_id
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * Returns email_sent
     *
     * @return int
     */
    public function getEmailSent()
    {
        return $this->_get(self::EMAIL_SENT);
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
     * Returns increment_id
     *
     * @return string
     */
    public function getIncrementId()
    {
        return $this->_get(self::INCREMENT_ID);
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
     * Returns packages
     *
     * @return string
     */
    public function getPackages()
    {
        return $this->_get(self::PACKAGES);
    }

    /**
     * Returns shipment_status
     *
     * @return int
     */
    public function getShipmentStatus()
    {
        return $this->_get(self::SHIPMENT_STATUS);
    }

    /**
     * Returns shipping_address_id
     *
     * @return int
     */
    public function getShippingAddressId()
    {
        return $this->_get(self::SHIPPING_ADDRESS_ID);
    }

    /**
     * Returns shipping_label
     *
     * @return string
     */
    public function getShippingLabel()
    {
        return $this->_get(self::SHIPPING_LABEL);
    }

    /**
     * Returns store_id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * Returns total_qty
     *
     * @return float
     */
    public function getTotalQty()
    {
        return $this->_get(self::TOTAL_QTY);
    }

    /**
     * Returns total_weight
     *
     * @return float
     */
    public function getTotalWeight()
    {
        return $this->_get(self::TOTAL_WEIGHT);
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
}
