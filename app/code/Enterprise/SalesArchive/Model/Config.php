<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order archive config model
 *
 */
class Enterprise_SalesArchive_Model_Config
{
    const XML_PATH_ARCHIVE_ACTIVE = 'sales/enterprise_salesarchive/active';
    const XML_PATH_ARCHIVE_AGE = 'sales/enterprise_salesarchive/age';
    const XML_PATH_ARCHIVE_ORDER_STATUSES = 'sales/enterprise_salesarchive/order_statuses';

    /**
     * Check archiving activity
     *
     * @return boolean
     */
    public function isArchiveActive()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ARCHIVE_ACTIVE);
    }

    /**
     * Retrieve archive age
     *
     * @return int
     */
    public function getArchiveAge()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_ARCHIVE_AGE);
    }

    /**
     * Retrieve order statuses for archiving
     *
     * @return array
     */
    public function getArchiveOrderStatuses()
    {
        $statuses = Mage::getStoreConfig(self::XML_PATH_ARCHIVE_ORDER_STATUSES);

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
