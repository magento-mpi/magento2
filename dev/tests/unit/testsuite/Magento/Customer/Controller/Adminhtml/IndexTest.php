<?php
/**
 * Unit test for \Magento\Customer\Controller\Adminhtml\Index controller
 *
 * {license_notice}
 *
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class \Magento\Customer\Controller\Adminhtml\IndexTest
 */
namespace Magento\Customer\Controller\Adminhtml;

class IndexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Request mock instance
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * Response mock instance
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\App\ResponseInterface
     */
    protected $_response;

    /**
     * Instance of mocked tested object
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Customer\Controller\Adminhtml\Index
     */
    protected $_testedObject;

    /**
     * ObjectManager mock instance
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\App\ObjectManager
     */
    protected $_objectManager;

    /**
     * Session mock instance
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Backend\Model\Session
     */
    protected $_session;

    /**
     * Backend helper mock instance
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Backend\Helper\Data
     */
    protected $_helper;

    /**
     * Prepare required values
     */
    protected function setUp()
    {
        $this->_request = $this->getMockBuilder('Magento\App\Request\Http')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_response = $this->getMockBuilder('Magento\App\Response\Http')
            ->disableOriginalConstructor()
            ->setMethods(array('setRedirect', 'getHeader'))
            ->getMock();

        $this->_response->expects($this->any())
            ->method('getHeader')
            ->with($this->equalTo('X-Frame-Options'))
            ->will($this->returnValue(true));

        $this->_objectManager = $this->getMock('Magento\ObjectManager');

        $this->_session = $this->getMockBuilder('Magento\Backend\Model\Session')
            ->disableOriginalConstructor()
            ->setMethods(array('setIsUrlNotice', 'addSuccess'))
            ->getMock();
        $this->_session->expects($this->any())->method('setIsUrlNotice');

        $this->_helper = $this->getMockBuilder('Magento\Backend\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(array('getUrl'))
            ->getMock();

        $helperObjectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_testedObject = $helperObjectManager->getObject('Magento\Customer\Controller\Adminhtml\Index',
            array(
                'helper' => $this->_helper,
                'session' => $this->_session,
                'objectManager' => $this->_objectManager,
                'request' => $this->_request,
                'response' => $this->_response

            )
        );
    }

    /**
     * Test \Magento\Adminhtml\Controller\Customer::resetPasswordAction()
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
            ->with($this->equalTo('customer/index'), $this->equalTo(array()))
            ->will($this->returnValue($redirectLink));

        $this->_response->expects($this->once())->method('setRedirect')->with($this->equalTo($redirectLink));
        $this->_testedObject->resetPasswordAction();
    }

    /**
     * Test \Magento\Adminhtml\Controller\Customer::resetPasswordAction()
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

        $customerMock = $this->_getCustomerMock($customerId, false);

        $this->_objectManager->expects($this->any())
            ->method('create')
            ->with($this->equalTo('Magento\Customer\Model\Customer'))
            ->will($this->returnValue($customerMock));

        $this->_helper->expects($this->any())
            ->method('getUrl')
            ->with($this->equalTo('customer/index'), $this->equalTo(array()))
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

        $customerHelperMock = $this->getMock('Magento\Customer\Helper\Data',
            array('generateResetPasswordLinkToken'), array(), '', false
        );
        $customerHelperMock->expects($this->any())
            ->method('generateResetPasswordLinkToken')
            ->will($this->returnValue($token));

        $coreHelperMock = $this->getMock('Magento\Core\Model\Url', array(), array(), '', false);
        $coreHelperMock->expects($this->any())->method('getUrl')->will($this->returnValue($testUrl));
        $this->_objectManager->expects($this->at(0))
            ->method('create')
            ->with($this->equalTo('Magento\Customer\Model\Customer'))
            ->will($this->returnValue($customerMock));

        $this->_objectManager->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('Magento\Customer\Helper\Data'))
            ->will($this->returnValue($customerHelperMock));

        $this->_objectManager->expects($this->at(2))
            ->method('create')
            ->with($this->equalTo('Magento\Core\Model\Url'))
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
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Customer\Model\Customer
     */
    protected function _getCustomerMock($customerId, $returnId = null)
    {
        $customerMock = $this->getMock('Magento\Customer\Model\Customer',
            array('setResetPasswordUrl', 'changeResetPasswordLinkToken', 'sendPasswordReminderEmail', 'load', 'getId'),
            array(), '', false);
        $customerMock->expects($this->any())
            ->method('load')
            ->with($this->equalTo($customerId));
        $customerMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($returnId));
        return $customerMock;
    }
}
