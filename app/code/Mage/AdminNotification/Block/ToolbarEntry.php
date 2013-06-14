<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Toolbar entry that shows latest notifications
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_AdminNotification_Block_ToolbarEntry extends Mage_Backend_Block_Template
{
    /**
     * Collection of latest unread notifications
     *
     * @var Mage_AdminNotification_Model_Resource_Inbox_Collection
     */
    protected $_notificationList;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_AdminNotification_Model_Resource_Inbox_Collection_Unread $notificationList
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_AdminNotification_Model_Resource_Inbox_Collection_Unread $notificationList,
        array $data = array()
    ) {
        parent::__construct($context, $data);
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
     * @return Mage_AdminNotification_Model_Resource_Inbox_Collection
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
            return $this->formatTime($dateString, Mage_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT, false);
        }
        return $this->formatDate($dateString, Mage_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM, true);
    }
}
