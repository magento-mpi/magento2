<?php
/**
 * Unit test for Magento_Adminhtml_Controller_Customer controller
 *
 * {license_notice}
 *
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class Magento_Adminhtml_Controller_CustomerTest
 */
class Magento_Adminhtml_Controller_CustomerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Request mock instance
     *
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * Response mock instance
     *
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Core_Controller_Response_Http
     */
    protected $_response;

    /**
     * Instance of mocked tested object
     *
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Adminhtml_Controller_Customer
     */
    protected $_testedObject;

    /**
     * ObjectManager mock instance
     *
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Core_Model_ObjectManager
     */
    protected $_objectManager;

    /**
     * Session mock instance
     *
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Backend_Model_Session
     */
    protected $_session;

    /**
     * Backend helper mock instance
     *
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Backend_Helper_Data
     */
    protected $_helper;

    /**
     * Prepare required values
     */
    public function setUp()
    {
        $this->_request = $this->getMockBuilder('Magento_Core_Controller_Request_Http')
            ->disableOriginalConstructor()
            ->setMethods(array('getParam'))
            ->getMock();

        $this->_response = $this->getMockBuilder('Magento_Core_Controller_Response_Http')
            ->disableOriginalConstructor()
            ->setMethods(array('setRedirect', 'getHeader'))
            ->getMock();
        $this->_response->expects($this->any())
            ->method('getHeader')
            ->with($this->equalTo('X-Frame-Options'))
            ->will($this->returnValue(true));

        $this->_objectManager = $this->getMockBuilder('Magento_Core_Model_ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('get', 'create'))
            ->getMock();
        $frontControllerMock = $this->getMockBuilder('Magento_Core_Controller_Varien_Front')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_session = $this->getMockBuilder('Magento_Backend_Model_Session')
            ->disableOriginalConstructor()
            ->setMethods(array('setIsUrlNotice', 'addSuccess'))
            ->getMock();
        $this->_session->expects($this->any())->method('setIsUrlNotice');

        $this->_helper = $this->getMockBuilder('Magento_Backend_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('getUrl'))
            ->getMock();

        $translator = $this->getMockBuilder('Magento_Core_Model_Translate')
            ->disableOriginalConstructor()
            ->setMethods(array('getTranslateInline'))
            ->getMock();

        $contextArgs = array(
            'getHelper', 'getSession', 'getAuthorization', 'getTranslator', 'getObjectManager', 'getFrontController',
            'getLayoutFactory', 'getEventManager', 'getRequest', 'getResponse'
        );
        $contextMock = $this->getMockBuilder('Magento_Backend_Controller_Context')
            ->disableOriginalConstructor()
            ->setMethods($contextArgs)
            ->getMock();
        $contextMock->expects($this->any())->method('getRequest')->will($this->returnValue($this->_request));
        $contextMock->expects($this->any())->method('getResponse')->will($this->returnValue($this->_response));
        $contextMock->expects($this->any())
            ->method('getObjectManager')
            ->will($this->returnValue($this->_objectManager));
        $contextMock->expects($this->any())
            ->method('getFrontController')
            ->will($this->returnValue($frontControllerMock));

        $contextMock->expects($this->any())->method('getHelper')->will($this->returnValue($this->_helper));
        $contextMock->expects($this->any())->method('getSession')->will($this->returnValue($this->_session));
        $contextMock->expects($this->any())->method('getTranslator')->will($this->returnValue($translator));

        $args = array('context' => $contextMock);

        $helperObjectManager = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_testedObject = $helperObjectManager->getObject('Magento_Adminhtml_Controller_Customer', $args);
    }

    /**
     * Test Magento_Adminhtml_Controller_Customer::resetPasswordAction()
     */
    public function testResetPasswordActionNoCustomer()
    {
        $redirectLink = 'http://example.com/customer/';
        $this->_request->expects($this->once())
            ->method('getParam')
            ->with($this->equalTo('customer_id'), $this->equalTo(0))
            ->will($this->returnValue(false)
        );
        $this->_helper->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo('*/customer'), $this->equalTo(array()))
            ->will($this->returnValue($redirectLink));

        $this->_response->expects($this->once())->method('setRedirect')->with($this->equalTo($redirectLink));
        $this->_testedObject->resetPasswordAction();
    }

    /**
     * Test Magento_Adminhtml_Controller_Customer::resetPasswordAction()
     */
    public function testResetPasswordActionNoCustomerId()
    {
        $redirectLink = 'http://example.com/customer/';
        $customerId = 1;

        $this->_request->expects($this->once())
            ->method('getParam')
            ->with($this->equalTo('customer_id'), $this->equalTo(0))
            ->will($this->returnValue($customerId)
        );

        $customerMock = $this->_getCustomerMock($customerId);

        $this->_objectManager->expects($this->any())
            ->method('create')
            ->with($this->equalTo('Magento_Customer_Model_Customer'))
            ->will($this->returnValue($customerMock));

        $this->_helper->expects($this->any())
            ->method('getUrl')
            ->with($this->equalTo('*/customer'), $this->equalTo(array()))
            ->will($this->returnValue($redirectLink));

        $this->_response->expects($this->once())->method('setRedirect')->with($this->equalTo($redirectLink));
        $this->_testedObject->resetPasswordAction();
    }

    /**
     * Test that sendPasswordReminderEmail() is called
     */
    public function testResetPasswordActionSendEmail()
    {
        $customerId = 1;
        $token = 2;
        $testUrl = 'http://example.com';

        $this->_request->expects($this->once())
            ->method('getParam')
            ->with($this->equalTo('customer_id'), $this->equalTo(0))
            ->will($this->returnValue($customerId)
        );

        $customerMock = $this->_getCustomerMock($customerId, true);
        $customerMock->expects($this->once())
            ->method('changeResetPasswordLinkToken')
            ->with($this->equalTo($token));
        $customerMock->expects($this->once())
            ->method('setResetPasswordUrl')
            ->with($this->equalTo($testUrl));
        $customerMock->expects($this->once())
            ->method('sendPasswordReminderEmail');

        $customerHelperMock = $this->getMockBuilder('Magento_Customer_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('generateResetPasswordLinkToken'))
            ->getMock();
        $customerHelperMock->expects($this->once())
            ->method('generateResetPasswordLinkToken')
            ->will($this->returnValue($token));

        $coreHelperMock = $this->getMockBuilder('Magento_Core_Model_Url')
            ->disableOriginalConstructor()
            ->setMethods(array('getUrl'))
            ->getMock();
        $coreHelperMock->expects($this->any())->method('getUrl')->will($this->returnValue($testUrl));

        $this->_objectManager->expects($this->at(0))
            ->method('create')
            ->with($this->equalTo('Magento_Customer_Model_Customer'))
            ->will($this->returnValue($customerMock));

        $this->_objectManager->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('Magento_Customer_Helper_Data'))
            ->will($this->returnValue($customerHelperMock));

        $this->_objectManager->expects($this->at(2))
            ->method('create')
            ->with($this->equalTo('Magento_Core_Model_Url'))
            ->will($this->returnValue($coreHelperMock));

        $this->_session->expects($this->once())
            ->method('addSuccess')
            ->with($this->equalTo('Customer will receive an email with a link to reset password.'));
        $this->_testedObject->resetPasswordAction();
    }

    /**
     * Return customer mock instance
     *
     * @param int $customerId
     * @param null|int $returnId
     * @return PHPUnit_Framework_MockObject_MockObject|Magento_Customer_Model_Customer
     */
    protected function _getCustomerMock($customerId, $returnId = null)
    {
        $customerMock = $this->getMockBuilder('Magento_Customer_Model_Customer')
            ->disableOriginalConstructor()
            ->setMethods(array('load', 'getId', 'changeResetPasswordLinkToken', 'setResetPasswordUrl',
                'sendPasswordReminderEmail'))
            ->getMock();
        $customerMock->expects($this->any())
            ->method('load')
            ->with($this->equalTo($customerId));
        $customerMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($returnId));
        return $customerMock;
    }
}
