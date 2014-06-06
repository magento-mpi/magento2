<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminNotification\Block;

/**
 * Toolbar entry that shows latest notifications
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class ToolbarEntry extends \Magento\Backend\Block\Template
{
    /**
     * Collection of latest unread notifications
     *
     * @var \Magento\AdminNotification\Model\Resource\Inbox\Collection
     */
    protected $_notificationList;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\AdminNotification\Model\Resource\Inbox\Collection\Unread $notificationList
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\AdminNotification\Model\Resource\Inbox\Collection\Unread $notificationList,
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
     * @param int $limit
     * @return \Magento\AdminNotification\Model\Resource\Inbox\Collection
     */
    public function getLatestUnreadNotifications($limit = null)
    {
        if (!empty($limit)) {
            $topNotes = $this->_notificationList->getItems();
            return array_slice($topNotes, 0, $limit);
        }
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
            return $this->formatTime(
                $dateString,
                \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT,
                false
            );
        }
        return $this->formatDate($dateString, \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_MEDIUM, true);
    }
}
