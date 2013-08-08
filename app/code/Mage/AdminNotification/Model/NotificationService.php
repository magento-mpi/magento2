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
 * Notification service model
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_AdminNotification_Model_NotificationService
{
    /**
     * @var Mage_AdminNotification_Model_InboxFactory $notificationFactory
     */
    protected $_notificationFactory;

    /**
     * @param Mage_AdminNotification_Model_InboxFactory $notificationFactory
     */
    public function __construct(
        Mage_AdminNotification_Model_InboxFactory $notificationFactory
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
