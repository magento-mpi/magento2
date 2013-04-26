<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_JobNotification_Model_InboxTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_JobNotification_Model_Inbox
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_observerMock;

    protected function setUp()
    {
        $this->_factoryMock = $this->getMock('Saas_JobNotification_Model_NotificationFactory',
            array('create'), array(), '', false
        );

        $this->_configMock = $this->getMock('Saas_JobNotification_Model_Config', array(), array(), '', false);
        $this->_eventMock = $this->getMock('Varien_Event', array('getTaskName'), array(), '', false);
        $this->_observerMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);
        $this->_observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($this->_eventMock));

        $this->_model = new Saas_JobNotification_Model_Inbox($this->_factoryMock, $this->_configMock);
    }

    public function testAddNotificationWithNotAllowedTask()
    {
        $this->markTestIncomplete('Need to be implemented in scope of MAGETWO-9658');
        //TODO: Implement logic here
        $this->_model->addNotification($this->_observerMock);
    }

    public function testAddNotificationWithAllowedTask()
    {
        $this->markTestIncomplete('Need to be implemented in scope of MAGETWO-9658');
        //TODO: Implement logic here

        $this->_model->addNotification($this->_observerMock);
    }
}