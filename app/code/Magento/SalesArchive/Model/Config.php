<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order archive config model
 *
 */
class Magento_SalesArchive_Model_Config
{
    const XML_PATH_ARCHIVE_ACTIVE = 'sales/magento_salesarchive/active';
    const XML_PATH_ARCHIVE_AGE = 'sales/magento_salesarchive/age';
    const XML_PATH_ARCHIVE_ORDER_STATUSES = 'sales/magento_salesarchive/order_statuses';

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig = null;

    /**
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
    }

    /**
     * Check archiving activity
     *
     * @return boolean
     */
    public function isArchiveActive()
    {
        return $this->_coreStoreConfig->getConfigFlag(self::XML_PATH_ARCHIVE_ACTIVE);
    }

    /**
     * Retrieve archive age
     *
     * @return int
     */
    public function getArchiveAge()
    {
        return (int) $this->_coreStoreConfig->getConfig(self::XML_PATH_ARCHIVE_AGE);
    }

    /**
     * Retrieve order statuses for archiving
     *
     * @return array
     */
    public function getArchiveOrderStatuses()
    {
        $statuses = $this->_coreStoreConfig->getConfig(self::XML_PATH_ARCHIVE_ORDER_STATUSES);

        if (empty($statuses)) {
            return array();
        }

        return explode(',', $statuses);
    }

    /**
     * Check order archiveablility for single archiving
     *
     * @param Magento_Sales_Model_Order $order
     * @param boolean $checkAge check order age for archive
     * @return boolean
     */
    public function isOrderArchiveable($order, $checkAge = false)
    {
        if (in_array($order->getStatus(), $this->getArchiveOrderStatuses())) {
            if ($checkAge) {
                $now = Mage::app()->getLocale()->storeDate();
                $updated = Mage::app()->getLocale()->storeDate($order->getUpdatedAt());

            }

            return true;
        }

        return false;
    }
}
