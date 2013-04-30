<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
require_once 'Saas/JobNotification/controllers/Adminhtml/ActionController.php';

class Saas_JobNotification_Adminhtml_ActionControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_JobNotification_Adminhtml_ActionController
     */
    protected $_controller;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_authorizationMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_responseMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_serviceMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sessionMock;

    protected function setUp()
    {
        $helper = new Magento_Test_Helper_ObjectManager($this);
        $this->_authorizationMock = $this->getMock('Mage_Core_Model_Authorization', array(), array(), '', false);
        $this->_responseMock = $this->getMock('Mage_Core_Controller_Response_Http', array(), array(), '', false);
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
        $this->_helperMock = $this->getMock('Saas_JobNotification_Helper_Data', array(), array(), '', false);
        $this->_serviceMock = $this->getMock('Saas_JobNotification_Service_Notification', array(), array(), '', false);
        $this->_sessionMock = $this->getMock('Mage_Backend_Model_Session', array(), array(), '', false);

        $arguments = array(
            'authorization' => $this->_authorizationMock,
            'response' => $this->_responseMock,
            'request' => $this->_requestMock,
            'helper' => $this->_helperMock,
            'service' => $this->_serviceMock,
            'session' => $this->_sessionMock,
        );

        $this->_responseMock->expects($this->once())->method('setRedirect')->with('*/view/index');
        $this->_helperMock->expects($this->once())
            ->method('getUrl')
            ->with('*/view/index')
            ->will($this->returnArgument(0));
        $this->_helperMock->expects($this->any())->method('__')->will($this->returnArgument(0));

        $this->_controller = $helper->getObject('Saas_JobNotification_Adminhtml_ActionController', $arguments);
    }

    public function testMarkAsReadActionWhenActionIsNotAllowed()
    {
        $this->_authorizationMock->expects($this->once())
            ->method('isAllowed')
            ->with('Saas_JobNotification::notification_action_markread')
            ->will($this->returnValue(false));

        $this->_serviceMock->expects($this->never())->method('update');

        $this->_controller->markAsReadAction();
    }

    public function testMarkAsReadActionSuccessUpdate()
    {
        $this->_authorizationMock->expects($this->once())
            ->method('isAllowed')
            ->with('Saas_JobNotification::notification_action_markread')
            ->will($this->returnValue(true));

        $id = '10';
        $this->_requestMock->expects($this->once())->method('getParam')->with('id')->will($this->returnValue($id));
        $this->_serviceMock->expects($this->once())->method('update')->with($id, array('is_read' => 1));
        $this->_sessionMock->expects($this->once())
            ->method('addSuccess')
            ->with('The notification has been marked as read');

        $this->_controller->markAsReadAction();
    }

    public function testMarkAsReadActionWithInvalidId()
    {
        $this->_authorizationMock->expects($this->once())
            ->method('isAllowed')
            ->with('Saas_JobNotification::notification_action_markread')
            ->will($this->returnValue(true));

        $id = '10';
        $this->_requestMock->expects($this->once())->method('getParam')->with('id')->will($this->returnValue($id));
        $this->_serviceMock->expects($this->once())->method('update')
            ->with($id, array('is_read' => 1))
            ->will($this->returnCallback(function(){
                throw new InvalidArgumentException();
            }));
        $this->_sessionMock->expects($this->never())->method('addSuccess');
        $this->_sessionMock->expects($this->once())->method('addError')->with('Unable to proceed. Please, try again');
        $this->_controller->markAsReadAction();
    }

    public function testMarkAsReadActionWithServiceException()
    {
        $this->_authorizationMock->expects($this->once())
            ->method('isAllowed')
            ->with('Saas_JobNotification::notification_action_markread')
            ->will($this->returnValue(true));

        $id = '10';
        $this->_requestMock->expects($this->once())->method('getParam')->with('id')->will($this->returnValue($id));
        $this->_serviceMock->expects($this->once())->method('update')
            ->with($id, array('is_read' => 1))
            ->will($this->returnCallback(function(){
                throw new Saas_JobNotification_Service_Exception('service message');
            }));
        $this->_sessionMock->expects($this->never())->method('addSuccess');
        $this->_sessionMock->expects($this->once())->method('addError')->with('service message');
        $this->_controller->markAsReadAction();
    }

    public function testMarkAsReadActionWithGeneralException()
    {
        $this->_authorizationMock->expects($this->once())
            ->method('isAllowed')
            ->with('Saas_JobNotification::notification_action_markread')
            ->will($this->returnValue(true));

        $id = '10';
        $this->_requestMock->expects($this->once())->method('getParam')->with('id')->will($this->returnValue($id));
        $this->_serviceMock->expects($this->once())->method('update')
            ->with($id, array('is_read' => 1))
            ->will($this->returnCallback(function(){
                throw new Exception();
            }));
        $this->_sessionMock->expects($this->never())->method('addSuccess');
        $this->_sessionMock->expects($this->never())->method('addError');
        $this->_sessionMock->expects($this->once())->method('addException');
        $this->_controller->markAsReadAction();
    }
}