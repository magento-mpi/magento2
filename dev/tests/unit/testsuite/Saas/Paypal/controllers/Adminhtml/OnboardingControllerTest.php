<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Paypal
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
require_once 'Saas/Paypal/controllers/Adminhtml/OnboardingController.php';

class Saas_Paypal_Adminhtml_OnboardingControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Paypal_Adminhtml_OnboardingController
     */
    protected $_controller;

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
    protected $_sessionMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_onboarding;

    protected function setUp()
    {
        $helper = new Magento_Test_Helper_ObjectManager($this);
        $this->_responseMock = $this->getMock('Mage_Core_Controller_Response_Http', array(), array(), '', false);
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
        $this->_helperMock = $this->getMock('Saas_JobNotification_Helper_Data', array(), array(), '', false);
        $this->_sessionMock = $this->getMock('Mage_Backend_Model_Session', array(), array(), '', false);
        $this->_onboarding = $this->getMock('Saas_Paypal_Model_Boarding_Onboarding', array(), array(), '', false);

        $arguments = array(
            'response' => $this->_responseMock,
            'request' => $this->_requestMock,
            'helper' => $this->_helperMock,
            'session' => $this->_sessionMock,
            'onboarding' => $this->_onboarding,
        );

        $this->_responseMock->expects($this->once())
            ->method('setRedirect')
            ->with($this->equalTo('*/system_config/edit'));
        $this->_helperMock->expects($this->once())
            ->method('getUrl')
            ->with(
                $this->equalTo('*/system_config/edit'),
                $this->equalTo(array('_current' => array('section', 'website', 'store')))
            )
            ->will($this->returnArgument(0));
        $this->_helperMock->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));

        $this->_controller = $helper->getObject('Saas_Paypal_Adminhtml_OnboardingController', $arguments);
    }

    public function testUpdateStatusActionPositive()
    {
        $token = 'TOKEN';
        $code = 'CODE';

        $this->_requestMock->expects($this->exactly(2))
            ->method('getParam')
            ->will($this->returnValueMap(array(
                array('request_token', null, $token),
                array('verification_code', null, $code),
            )));

        $this->_onboarding->expects($this->once())
            ->method('updateMethodStatus')
            ->with($this->equalTo($token), $this->equalTo($code))
            ->will($this->returnValue(true));

        $this->_sessionMock->expects($this->once())
            ->method('addSuccess');
        $this->_sessionMock->expects($this->never())
            ->method('addError');

        $this->_controller->updateStatusAction();
    }

    public function testUpdateStatusActionNegativeUpdate()
    {
        $token = 'TOKEN';
        $code = 'CODE';

        $this->_requestMock->expects($this->exactly(2))
            ->method('getParam')
            ->will($this->returnValueMap(array(
                array('request_token', null, $token),
                array('verification_code', null, $code),
            )));

        $this->_onboarding->expects($this->once())
            ->method('updateMethodStatus')
            ->with($this->equalTo($token), $this->equalTo($code))
            ->will($this->returnValue(false));

        $this->_sessionMock->expects($this->never())
            ->method('addSuccess');
        $this->_sessionMock->expects($this->once())
            ->method('addError');

        $this->_controller->updateStatusAction();
    }

    /**
     * @param $token string
     * @param $code string
     *
     * @dataProvider dataProviderUpdateStatusActionNegativeParameters
     */
    public function testUpdateStatusActionNegativeParameters($token, $code)
    {
        $this->_requestMock->expects($this->exactly(2))
            ->method('getParam')
            ->will($this->returnValueMap(array(
                array('request_token', null, $token),
                array('verification_code', null, $code),
            )));

        $this->_onboarding->expects($this->never())
            ->method('updateMethodStatus');

        $this->_sessionMock->expects($this->never())
            ->method('addSuccess');
        $this->_sessionMock->expects($this->never())
            ->method('addError');

        $this->_controller->updateStatusAction();
    }

    public function dataProviderUpdateStatusActionNegativeParameters()
    {
        return array(
            array('TOKEN', ''),
            array('', 'CODE'),
            array('', ''),
        );
    }
}
