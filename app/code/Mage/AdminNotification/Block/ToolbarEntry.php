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
     * The number of latest notifications shown in notification toolbar entry
     */
    const SHOWN_NOTIFICATION_COUNT = 5;

    /**
     * Collection of latest unread notifications
     *
     * @var Mage_AdminNotification_Model_Resource_Inbox_Collection
     */
    protected $_notificationList;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_AdminNotification_Model_Resource_Inbox_UnreadNotificationCollection $notificationList
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_AdminNotification_Model_Resource_Inbox_UnreadNotificationCollection $notificationList,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_notificationList = $notificationList;
        // set view-specific limitations to notification collection
        $this->_notificationList->setCurPage(1);
        $this->_notificationList->setPageSize(self::SHOWN_NOTIFICATION_COUNT);
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
}
