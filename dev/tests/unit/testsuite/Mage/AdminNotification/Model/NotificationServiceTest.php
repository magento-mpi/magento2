<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_AdminNotification_Model_NotificationService
 */
class Mage_AdminNotification_Model_NotificationServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Retrieve instance of notification service model
     *
     * @param $notificationId
     * @return Mage_AdminNotification_Model_NotificationService
     */
    protected function _getServiceInstanceForMarkAsReadTest($notificationId)
    {
        /**
         * @var $notificationFactory PHPUnit_Framework_MockObject_MockObject|Mage_AdminNotification_Model_InboxFactory
         */
        $notificationFactory = $this->getMock(
            'Mage_AdminNotification_Model_InboxFactory', array('create'), array(), '', false
        );
        $notification = $this->getMock(
            'Mage_AdminNotification_Model_Inbox',
            array('load', 'getId', 'save','setIsRead'),
            array(),
            '',
            false
        );
        $notification->expects($this->once())->method('load')->with($notificationId)->will($this->returnSelf());
        $notification->expects($this->once())->method('getId')->will($this->returnValue($notificationId));

        // when notification Id is valid, add additional expectations
        if ($notificationId) {
            $notification->expects($this->once())->method('save')->will($this->returnSelf());
            $notification->expects($this->once())->method('setIsRead')->with(1)->will($this->returnSelf());
        }

        $notificationFactory->expects($this->once())->method('create')->will($this->returnValue($notification));
        return new Mage_AdminNotification_Model_NotificationService($notificationFactory);
    }

    public function testMarkAsRead()
    {
        $notificationId = 1;
        $service = $this->_getServiceInstanceForMarkAsReadTest($notificationId);
        $service->markAsRead($notificationId);
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage Wrong notification ID specified.
     */
    public function testMarkAsReadThrowsExceptionWhenNotificationIdIsInvalid()
    {
        $notificationId = null;
        $service = $this->_getServiceInstanceForMarkAsReadTest($notificationId);
        $service->markAsRead($notificationId);
    }
}
