<?php
/**
 * Unit test for Mage_Adminhtml_CustomerController controller
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
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
     * Tested class name
     *
     * @var string
     */
    protected $_testClassName = 'Mage_Adminhtml_CustomerController';

    /**
     * Test Mage_Adminhtml_CustomerController::resetPasswordAction()
     */
    public function testResetPasswordActionNoCustomer()
    {
        $requestMock = $this->_prepareRequestMock();
        $controllerMock = $this->getMockBuilder($this->_testClassName)
            ->disableOriginalConstructor()
            ->setMethods(array('getRequest', '_redirect'))
            ->getMock();
        $controllerMock->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($requestMock));
        $controllerMock->expects($this->once())
            ->method('_redirect')
            ->with($this->equalTo('*/customer'))
            ->will($this->returnValue($controllerMock));
        $result = $controllerMock->resetPasswordAction();
        $this->assertInstanceOf($this->_testClassName, $result);
    }

    /**
     * Test Mage_Adminhtml_CustomerController::resetPasswordAction()
     */
    public function testResetPasswordActionNoCustomerId()
    {
        $customerId = rand(1, 10000);
        $requestHttpMock = $this->_prepareRequestMock($customerId);

        $arguments = $this->_getArguments();

        $customerMock = $this->getMockBuilder('Mage_Customer_Model_Customer')
            ->disableOriginalConstructor()
            ->setMethods(array('load', 'getId', 'changeResetPasswordLinkToken', 'setResetPasswordUrl',
                'sendPasswordReminderEmail'))
            ->getMock();
        $customerMock->expects($this->once())
            ->method('load')
            ->with($this->equalTo($customerId));
        $customerMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(null));

        $objectManagerMock = $this->getMockBuilder('Mage_Core_Model_ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('get', 'create'))
            ->getMock();
        $objectManagerMock->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('Mage_Customer_Model_Customer'))
            ->will($this->returnValue($customerMock));

        $controllerMock = $this->getMockBuilder($this->_testClassName)
            ->setConstructorArgs(
                array(
                    $requestHttpMock,
                    $arguments['response'],
                    $objectManagerMock,
                    $arguments['front'],
                    $arguments['layout'],
                    $arguments['area'],
                    $arguments['args']
            ))
            ->setMethods(array('getRequest', '_redirect'))
            ->getMock();
        $controllerMock->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($requestHttpMock));
        $controllerMock->expects($this->once())
            ->method('_redirect')
            ->with($this->equalTo('*/customer'))
            ->will($this->returnValue($controllerMock));
        $result = $controllerMock->resetPasswordAction();
        $this->assertInstanceOf($this->_testClassName, $result);
    }

    /**
     * Test Mage_Adminhtml_CustomerController::resetPasswordAction()
     * Normal behaviour
     */
    public function testResetPasswordActionSendEmail()
    {
        $customerId = rand(1, 10000);
        $token = md5(time());
        $testUrl = 'example.com';
        $requestHttpMock = $this->_prepareRequestMock($customerId);

        $arguments = $this->_getArguments();

        $customerMock = $this->getMockBuilder('Mage_Customer_Model_Customer')
            ->disableOriginalConstructor()
            ->setMethods(array('load', 'getId', 'changeResetPasswordLinkToken', 'setResetPasswordUrl',
                'sendPasswordReminderEmail'))
            ->getMock();
        $customerMock->expects($this->once())
            ->method('load')
            ->with($this->equalTo($customerId));
        $customerMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($customerId));
        $customerMock->expects($this->once())
            ->method('changeResetPasswordLinkToken')
            ->with($this->equalTo($token));
        $customerMock->expects($this->once())
            ->method('setResetPasswordUrl')
            ->with($this->equalTo($testUrl));
        $customerMock->expects($this->once())
            ->method('sendPasswordReminderEmail');

        $sessionMock = $this->getMockBuilder('Mage_Backend_Model_Session')
            ->disableOriginalConstructor()
            ->setMethods(array('addSuccess'))
            ->getMock();
        $sessionMock->expects($this->any())
            ->method('addSuccess');

        $backendHelperMock = $this->getMockBuilder('Mage_Backend_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('__'))
            ->getMock();
        $backendHelperMock->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));

        $customerHelperMock = $this->getMockBuilder('Mage_Customer_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('generateResetPasswordLinkToken'))
            ->getMock();
        $customerHelperMock->expects($this->once())
            ->method('generateResetPasswordLinkToken')
            ->will($this->returnValue($token));

        $coreUrlMock = $this->getMockBuilder('Mage_Core_Model_Url')
            ->disableOriginalConstructor()
            ->setMethods(array('getUrl'))
            ->getMock();
        $coreUrlMock->expects($this->once())
            ->method('getUrl')
            ->with(
                $this->equalTo('customer/account/createPassword'),
                $this->equalTo(array(
                    '_query' => array(
                        'id' => $customerId,
                        'token' => $token
                    )
                )))
            ->will($this->returnValue($testUrl));

        $objectManagerMock = $this->getMockBuilder('Mage_Core_Model_ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('get', 'create'))
            ->getMock();
        $objectManagerMock->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('Mage_Customer_Model_Customer'))
            ->will($this->returnValue($customerMock));
        $objectManagerMock->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('Mage_Customer_Helper_Data'))
            ->will($this->returnValue($customerHelperMock));
        $objectManagerMock->expects($this->at(2))
            ->method('create')
            ->with($this->equalTo('Mage_Core_Model_Url'))
            ->will($this->returnValue($coreUrlMock));

        $controllerMock = $this->getMockBuilder($this->_testClassName)
            ->setConstructorArgs(
                array(
                    $requestHttpMock,
                    $arguments['response'],
                    $objectManagerMock,
                    $arguments['front'],
                    $arguments['layout'],
                    $arguments['area'],
                    $arguments['args']
                ))
            ->setMethods(array('getRequest', '_getSession', '_getHelper', '_redirect'))
            ->getMock();
        $controllerMock->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($requestHttpMock));
        $controllerMock->expects($this->once())
            ->method('_getSession')
            ->will($this->returnValue($sessionMock));
        $controllerMock->expects($this->once())
            ->method('_getHelper')
            ->will($this->returnValue($backendHelperMock));
        $controllerMock->expects($this->once())
            ->method('_redirect')
            ->with($this->equalTo('*/*/edit'), $this->equalTo(array('id' => $customerId, '_current' => true)))
            ->will($this->returnValue($controllerMock));
        $controllerMock->resetPasswordAction();
    }

    /**
     * Return request mock object
     *
     * @param null $customerId
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _prepareRequestMock($customerId = null)
    {
        $requestMock = $this->getMockBuilder('Mage_Core_Controller_Request_Http')
            ->disableOriginalConstructor()
            ->setMethods(array('getParam'))
            ->getMock();
        $requestMock->expects($this->once())
            ->method('getParam')
            ->with($this->equalTo('customer_id'), $this->equalTo(0))
            ->will($this->returnValue($customerId));
        return $requestMock;
    }

    /**
     * Return constructor arguments
     *
     * @return array
     */
    protected function _getArguments()
    {
        $arguments = array();
        $arguments['response'] = $responseHttpMock = $this->getMockBuilder('Mage_Core_Controller_Response_Http')
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['front'] = $this->getMockBuilder('Mage_Core_Controller_Varien_Front')
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['layout'] = $this->getMockBuilder('Mage_Core_Model_Layout_Factory')
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['area'] = 'test';
        $arguments['args'] = array(
            'translator' => new Varien_Object(),
            'helper' => new Varien_Object(),
            'session' => new Varien_Object()
        );
        return $arguments;
    }
}
