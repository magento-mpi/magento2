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
 * Notification service model
 *
 * @category    Magento
 * @package     Magento_AdminNotification
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdminNotification_Model_NotificationService
{
    /**
     * @var Magento_AdminNotification_Model_InboxFactory $notificationFactory
     */
    protected $_notificationFactory;

    /**
     * @param Magento_AdminNotification_Model_InboxFactory $notificationFactory
     */
    public function __construct(
        Magento_AdminNotification_Model_InboxFactory $notificationFactory
    ) {
        $this->_notificationFactory = $notificationFactory;
    }

    /**
     * Mark notification as read
     *
     * @param int $notificationId
     * @throws Magento_Core_Exception
     */
    public function markAsRead($notificationId)
    {
        $notification = $this->_notificationFactory->create();
        $notification->load($notificationId);
        if (!$notification->getId()) {
            throw new Magento_Core_Exception('Wrong notification ID specified.');
        }
        $notification->setIsRead(1);
        $notification->save();
    }
}
