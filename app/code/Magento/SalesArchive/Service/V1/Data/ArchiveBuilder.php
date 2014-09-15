<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Service\V1\Data;

/**
 * Builder class for \Magento\SalesArchive\Service\V1\Data\Archive
 *
 * @codeCoverageIgnore
 */
class ArchiveBuilder extends \Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder
{
    /**
     * @param string $baseCurrencyCode
     *
     * @return $this
     */
    public function setBaseCurrencyCode($baseCurrencyCode)
    {
        return $this->_set(Archive::BASE_CURRENCY_CODE, $baseCurrencyCode);
    }

    /**
     * @param float $baseGrandTotal
     *
     * @return $this
     */
    public function setBaseGrandTotal($baseGrandTotal)
    {
        return $this->_set(Archive::BASE_GRAND_TOTAL, $baseGrandTotal);
    }

    /**
     * @param float $baseTotalPaid
     *
     * @return $this
     */
    public function setBaseTotalPaid($baseTotalPaid)
    {
        return $this->_set(Archive::BASE_TOTAL_PAID, $baseTotalPaid);
    }

    /**
     * @param string $billingName
     *
     * @return $this
     */
    public function setBillingName($billingName)
    {
        return $this->_set(Archive::BILLING_NAME, $billingName);
    }

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->_set(Archive::CREATED_AT, $createdAt);
    }

    /**
     * @param int $customerId
     *
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->_set(Archive::CUSTOMER_ID, $customerId);
    }

    /**
     * @param int $entityId
     *
     * @return $this
     */
    public function setEntityId($entityId)
    {
        return $this->_set(Archive::ENTITY_ID, $entityId);
    }

    /**
     * @param float $grandTotal
     *
     * @return $this
     */
    public function setGrandTotal($grandTotal)
    {
        return $this->_set(Archive::GRAND_TOTAL, $grandTotal);
    }

    /**
     * @param string $incrementId
     *
     * @return $this
     */
    public function setIncrementId($incrementId)
    {
        return $this->_set(Archive::INCREMENT_ID, $incrementId);
    }

    /**
     * @param string $orderCurrencyCode
     *
     * @return $this
     */
    public function setOrderCurrencyCode($orderCurrencyCode)
    {
        return $this->_set(Archive::ORDER_CURRENCY_CODE, $orderCurrencyCode);
    }

    /**
     * @param string $shippingName
     *
     * @return $this
     */
    public function setShippingName($shippingName)
    {
        return $this->_set(Archive::SHIPPING_NAME, $shippingName);
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->_set(Archive::STATUS, $status);
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->_set(Archive::STORE_ID, $storeId);
    }

    /**
     * @param string $storeName
     *
     * @return $this
     */
    public function setStoreName($storeName)
    {
        return $this->_set(Archive::STORE_NAME, $storeName);
    }

    /**
     * @param float $totalPaid
     *
     * @return $this
     */
    public function setTotalPaid($totalPaid)
    {
        return $this->_set(Archive::TOTAL_PAID, $totalPaid);
    }

    /**
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->_set(Archive::UPDATED_AT, $updatedAt);
    }
}
