<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_JobNotification_Service_NotificationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_JobNotification_Service_Notification
     */
    private $_service;

    /**
     * @var Saas_JobNotification_Model_NotificationFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $_notificationFactory;

    protected function setUp()
    {
        $this->_notificationFactory = $this->getMock(
            'Saas_JobNotification_Model_NotificationFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->_service = new Saas_JobNotification_Service_Notification($this->_notificationFactory);
    }

    /**
     * Retrieve mocked notification instance
     *
     * @param int|null $notificationId
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getNotification($notificationId)
    {
        $notification = $this->getMock('Saas_JobNotification_Model_Notification',
            array('save', 'load', 'addData', 'getId'),
            array(),
            '',
            false
        );
        $notification->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($notificationId));
        return $notification;
    }

    public function testUpdate()
    {
        $notificationId = 1;
        $data = array('remove' => 1);
        $notification = $this->_getNotification($notificationId);
        $notification->expects($this->once())
            ->method('addData')
            ->with($data)
            ->will($this->returnValue($notification));
        $notification->expects($this->once())
            ->method('save');
        $this->_notificationFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($notification));
        $this->_service->update($notificationId, $data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid notification id
     */
    public function testUpdateThrowsExceptionWhenNotificationIdIsInvalid()
    {
        $data = array('remove' => 1);
        $notificationId = null;
        $notification = $this->_getNotification($notificationId);
        $this->_notificationFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($notification));
        $this->_service->update($notificationId, $data);
    }

    public function testMassUpdate()
    {
        $notificationId = 1;
        $data = array('remove' => 1);
        $notification = $this->_getNotification($notificationId);
        $notification->expects($this->once())
            ->method('save');
        $notification->expects($this->once())
            ->method('addData')
            ->with($data)
            ->will($this->returnValue($notification));
        $this->_notificationFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($notification));
        $this->_service->massUpdate(array($notificationId), $data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid notification ids list
     */
    public function testMassUpdateThrowsExceptionWhenNotificationIdListIsEmpty()
    {
        $data = array('remove' => 1);
        $this->_service->massUpdate(array(), $data);
    }

    public function testMassUpdateDoesNotThrowExceptionWhenNotificationIdIsInvalid()
    {
        $notificationId = null;
        $notification = $this->_getNotification($notificationId);
        $data = array('remove' => 1);
        $this->_notificationFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($notification));
        $this->_service->massUpdate(array($notificationId), $data);
    }
}
