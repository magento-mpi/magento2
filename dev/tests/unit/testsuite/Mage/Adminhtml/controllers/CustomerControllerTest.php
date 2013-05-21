<?php
/**
 * Unit test for Mage_Adminhtml_CustomerController controller
 *
 * {license_notice}
 *
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require 'Mage/Adminhtml/controllers/CustomerController.php';

/**
 * Class Mage_Adminhtml_CustomerControllerTest
 */
class Mage_Adminhtml_CustomerControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Request mock instance
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    /**
     * Response mock instance
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_response;

    /**
     * Instance of mocked tested object
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * ObjectManager mock instance
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * Session mock instance
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_session;

    /**
     * Backend helper mock instance
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helper;

    /**
     * Prepare required values
     */
    public function setUp()
    {
        $this->_request = $this->getMockBuilder('Mage_Core_Controller_Request_Http')
            ->disableOriginalConstructor()
            ->setMethods(array('getParam'))
            ->getMock();

        $this->_response = $this->getMockBuilder('Mage_Core_Controller_Response_Http')
            ->disableOriginalConstructor()
            ->setMethods(array('setRedirect'))
            ->getMock();

        $this->_objectManager = $this->getMockBuilder('Mage_Core_Model_ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('get', 'create'))
            ->getMock();
        $frontControllerMock = $this->getMockBuilder('Mage_Core_Controller_Varien_Front')
            ->disableOriginalConstructor()
            ->getMock();
        $layoutFactory = $this->getMockBuilder('Mage_Core_Model_Layout_Factory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_session = $this->getMockBuilder('Mage_Backend_Model_Session')
            ->disableOriginalConstructor()
            ->setMethods(array('setIsUrlNotice', 'addSuccess'))
            ->getMock();
        $this->_session->expects($this->any())->method('setIsUrlNotice');
        $this->_session->expects($this->any())->method('addSuccess');

        $this->_helper = $this->getMockBuilder('Mage_Backend_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('getUrl', '__'))
            ->getMock();
        $this->_helper->expects($this->any())->method('__');

        $invokeArgs = array('translator' => 'translator', 'helper' => $this->_helper, 'session' => $this->_session);

        $arguments = array($this->_request, $this->_response, $this->_objectManager, $frontControllerMock,
            $layoutFactory, 'test_area_code', $invokeArgs
        );
        $this->_model = $this->getMockBuilder('Mage_Adminhtml_CustomerController')
            ->setConstructorArgs($arguments)
            ->setMethods(array('loadLayout', 'getUrl'))
            ->getMock();
    }

    /**
     * Test Mage_Adminhtml_CustomerController::resetPasswordAction()
     */
    public function testResetPasswordActionNoCustomer()
    {
        $this->_request->expects($this->once())
            ->method('getParam')
            ->with($this->equalTo('customer_id'), $this->equalTo(0))
            ->will($this->returnValue(false)
        );
        $this->_helper->expects($this->any())
            ->method('getUrl')
            ->with($this->equalTo('*/customer'), $this->equalTo(array()));

        $this->_model->resetPasswordAction();
    }

    /**
     * Test Mage_Adminhtml_CustomerController::resetPasswordAction()
     */
    public function testResetPasswordActionNoCustomerId()
    {
        $customerId = rand(1, 10000);

        $this->_request->expects($this->once())
            ->method('getParam')
            ->with($this->equalTo('customer_id'), $this->equalTo(0))
            ->will($this->returnValue($customerId)
        );

        $customerMock = $this->_returnCustomerMock($customerId);

        $this->_objectManager->expects($this->any())
            ->method('create')
            ->with($this->equalTo('Mage_Customer_Model_Customer'))
            ->will($this->returnValue($customerMock));

        $this->_helper->expects($this->any())
            ->method('getUrl')
            ->with($this->equalTo('*/customer'), $this->equalTo(array()));

        $this->_model->resetPasswordAction();
    }

    /**
     * Test that sendPasswordReminderEmail() is called
     */
    public function testResetPasswordActionSendEmail()
    {
        $customerId = rand(1, 10000);
        $token = md5(time());
        $testUrl = 'http://example.com';

        $this->_request->expects($this->once())
            ->method('getParam')
            ->with($this->equalTo('customer_id'), $this->equalTo(0))
            ->will($this->returnValue($customerId)
        );

        $customerMock = $this->_returnCustomerMock($customerId, true);
        $customerMock->expects($this->once())
            ->method('changeResetPasswordLinkToken')
            ->with($this->equalTo($token));
        $customerMock->expects($this->once())
            ->method('setResetPasswordUrl')
            ->with($this->equalTo($testUrl));
        $customerMock->expects($this->once())
            ->method('sendPasswordReminderEmail');

        $customerHelperMock = $this->getMockBuilder('Mage_Customer_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('generateResetPasswordLinkToken'))
            ->getMock();
        $customerHelperMock->expects($this->once())
            ->method('generateResetPasswordLinkToken')
            ->will($this->returnValue($token));

        $coreHelperMock = $this->getMockBuilder('Mage_Core_Model_Url')
            ->disableOriginalConstructor()
            ->setMethods(array('getUrl'))
            ->getMock();
        $coreHelperMock->expects($this->any())->method('getUrl')->will($this->returnValue($testUrl));

        $this->_objectManager->expects($this->at(0))
            ->method('create')
            ->with($this->equalTo('Mage_Customer_Model_Customer'))
            ->will($this->returnValue($customerMock));

        $this->_objectManager->expects($this->at(1))
            ->method('create')
            ->with($this->equalTo('Mage_Customer_Helper_Data'))
            ->will($this->returnValue($customerHelperMock));

        $this->_objectManager->expects($this->at(2))
            ->method('create')
            ->with($this->equalTo('Mage_Core_Model_Url'))
            ->will($this->returnValue($coreHelperMock));

        $this->_model->resetPasswordAction();
    }

     /**
     * Return customer mock instance
     *
     * @param int $customerId
     * @param null|int $id
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _returnCustomerMock($customerId, $id = null)
    {
        $customerMock = $this->getMockBuilder('Mage_Customer_Model_Customer')
            ->disableOriginalConstructor()
            ->setMethods(array('load', 'getId', 'changeResetPasswordLinkToken', 'setResetPasswordUrl',
                'sendPasswordReminderEmail'))
            ->getMock();
        $customerMock->expects($this->any())
            ->method('load')
            ->with($this->equalTo($customerId));
        $customerMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($id));
        return $customerMock;
    }
}
