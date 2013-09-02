<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Toolbar entry that shows latest notifications
 *
 * @category    Magento
 * @package     Magento_AdminNotification
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdminNotification_Block_ToolbarEntry extends Magento_Backend_Block_Template
{
    /**
     * Collection of latest unread notifications
     *
     * @var Magento_AdminNotification_Model_Resource_Inbox_Collection
     */
    protected $_notificationList;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_AdminNotification_Model_Resource_Inbox_Collection_Unread $notificationList
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_AdminNotification_Model_Resource_Inbox_Collection_Unread $notificationList,
        array $data = array()
    ) {
        parent::__construct($context, $coreStoreConfig, $data);
        $this->_notificationList = $notificationList;
    }

    /**
     * Retrieve number of unread notifications
     *
     * @return int
     */
    public function getUnreadNotificationCount()
    {
        return $this->_notificationList->getSize();
    }

    /**
     * Retrieve the list of latest unread notifications
     *
     * @return Magento_AdminNotification_Model_Resource_Inbox_Collection
     */
    public function getLatestUnreadNotifications()
    {
        return $this->_notificationList;
    }

    /**
     * Format notification date (show only time if notification has been added today)
     *
     * @param string $dateString
     * @return string
     */
    public function formatNotificationDate($dateString)
    {
        if (date('Ymd') == date('Ymd', strtotime($dateString))) {
            return $this->formatTime($dateString, Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT, false);
        }
        return $this->formatDate($dateString, Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM, true);
    }
}
