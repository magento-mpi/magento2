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
namespace Magento\AdminNotification\Block;

class ToolbarEntry extends \Magento\Backend\Block\Template
{
    /**
     * Collection of latest unread notifications
     *
     * @var \Magento\AdminNotification\Model\Resource\Inbox\Collection
     */
    protected $_notificationList;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\AdminNotification\Model\Resource\Inbox\Collection\Unread $notificationList
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\AdminNotification\Model\Resource\Inbox\Collection\Unread $notificationList,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
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
     * @return \Magento\AdminNotification\Model\Resource\Inbox\Collection
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
            return $this->formatTime($dateString, \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT, false);
        }
        return $this->formatDate($dateString, \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM, true);
    }
}
