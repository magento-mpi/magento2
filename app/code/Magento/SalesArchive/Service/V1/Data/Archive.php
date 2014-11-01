<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Service\V1\Data;

use Magento\Framework\Api\AbstractExtensibleObject as DataObject;

/**
 * Class Archive
 *
 * @codeCoverageIgnore
 */
class Archive extends DataObject
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const ENTITY_ID = 'entity_id';
    const STATUS = 'status';
    const STORE_ID = 'store_id';
    const STORE_NAME = 'store_name';
    const CUSTOMER_ID = 'customer_id';
    const BASE_GRAND_TOTAL = 'base_grand_total';
    const BASE_TOTAL_PAID = 'base_total_paid';
    const GRAND_TOTAL = 'grand_total';
    const TOTAL_PAID = 'total_paid';
    const INCREMENT_ID = 'increment_id';
    const BASE_CURRENCY_CODE = 'base_currency_code';
    const ORDER_CURRENCY_CODE = 'order_currency_code';
    const SHIPPING_NAME = 'shipping_name';
    const BILLING_NAME = 'billing_name';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    /**#@-*/

    /**
     * Returns base_currency_code
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        return $this->_get(self::BASE_CURRENCY_CODE);
    }

    /**
     * Returns base_grand_total
     *
     * @return float
     */
    public function getBaseGrandTotal()
    {
        return $this->_get(self::BASE_GRAND_TOTAL);
    }

    /**
     * Returns base_total_paid
     *
     * @return float
     */
    public function getBaseTotalPaid()
    {
        return $this->_get(self::BASE_TOTAL_PAID);
    }

    /**
     * Returns billing_name
     *
     * @return string
     */
    public function getBillingName()
    {
        return $this->_get(self::BILLING_NAME);
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
     * Returns entity_id
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->_get(self::ENTITY_ID);
    }

    /**
     * Returns grand_total
     *
     * @return float
     */
    public function getGrandTotal()
    {
        return $this->_get(self::GRAND_TOTAL);
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
     * Returns order_currency_code
     *
     * @return string
     */
    public function getOrderCurrencyCode()
    {
        return $this->_get(self::ORDER_CURRENCY_CODE);
    }

    /**
     * Returns shipping_name
     *
     * @return string
     */
    public function getShippingName()
    {
        return $this->_get(self::SHIPPING_NAME);
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
     * Returns store_id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * Returns store_name
     *
     * @return string
     */
    public function getStoreName()
    {
        return $this->_get(self::STORE_NAME);
    }

    /**
     * Returns total_paid
     *
     * @return float
     */
    public function getTotalPaid()
    {
        return $this->_get(self::TOTAL_PAID);
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
