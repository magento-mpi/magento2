<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Captcha_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Captcha_Model_Observer
     */
    protected $_observer;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helper;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_session;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlManager;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_captcha;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_typeOnepage;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resLogFactory;

    protected function setUp()
    {
        $this->_resLogFactory = $this->getMock('Magento_Captcha_Model_Resource_LogFactory',
            array('create'), array(), '', false);
        $this->_resLogFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_getResourceModelStub()));

        $this->_typeOnepage = $this->getMock('Magento_Checkout_Model_Type_Onepage', array(), array(), '', false);
        $this->_session = $this->getMock('Magento_Core_Model_Session_Abstract', array(), array(), '', false);
        $this->_backendSession = $this->getMock('Magento_Backend_Model_Session', array(), array(), '', false);
        $this->_helper = $this->getMock('Magento_Captcha_Helper_Data', array(), array(), '', false);
        $this->_urlManager = $this->getMock('Magento_Core_Model_Url', array(), array(), '', false);
        $this->_filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $this->_coreData = $this->getMock('Magento_Core_Helper_Data', array(), array(), '', false);
        $this->_customerData = $this->getMock('Magento_Customer_Helper_Data', array(), array(), '', false);

        $this->_observer = new Magento_Captcha_Model_Observer(
            $this->_resLogFactory,
            $this->_session,
            $this->_typeOnepage,
            $this->_coreData,
            $this->_customerData,
            $this->_helper,
            $this->_urlManager,
            $this->_filesystem
        );
        $this->_captcha = $this->getMock('Magento_Captcha_Model_Default', array(), array(), '', false);
    }

    public function testCheckContactUsFormWhenCaptchaIsRequiredAndValid()
    {
        $formId = 'contact_us';
        $captchaValue = 'some-value';

        $controller = $this->getMock('Magento_Core_Controller_Varien_Action', array(), array(), '', false);
        $request = $this->getMock('Magento_Core_Controller_Request_Http', array(), array(), '', false);
        $request->expects($this->any())
            ->method('getPost')
            ->with(Magento_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE, null)
            ->will($this->returnValue(array(
                $formId => $captchaValue,
            )));
        $controller->expects($this->any())->method('getRequest')->will($this->returnValue($request));
        $this->_captcha->expects($this->any())
            ->method('isRequired')
            ->will($this->returnValue(true));
        $this->_captcha->expects($this->once())
            ->method('isCorrect')
            ->with($captchaValue)
            ->will($this->returnValue(true));
        $this->_helper->expects($this->any())
            ->method('getCaptcha')
            ->with($formId)
            ->will($this->returnValue($this->_captcha));
        $this->_session->expects($this->never())->method('addError');

        $this->_observer->checkContactUsForm(new Magento_Event_Observer(array('controller_action' => $controller)));
    }

    public function testCheckContactUsFormRedirectsCustomerWithWarningMessageWhenCaptchaIsRequiredAndInvalid()
    {
        $formId = 'contact_us';
        $captchaValue = 'some-value';
        $warningMessage = 'Incorrect CAPTCHA.';
        $redirectRoutePath = 'contacts/index/index';
        $redirectUrl = 'http://magento.com/contacts/';

        $this->_urlManager->expects($this->once())
            ->method('getUrl')
            ->with($redirectRoutePath, null)
            ->will($this->returnValue($redirectUrl));

        $controller = $this->getMock('Magento_Core_Controller_Varien_Action', array(), array(), '', false);
        $request = $this->getMock('Magento_Core_Controller_Request_Http', array(), array(), '', false);
        $response = $this->getMock('Magento_Core_Controller_Response_Http', array(), array(), '', false);
        $request->expects($this->any())->method('getPost')->with(Magento_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE,
            null)
            ->will($this->returnValue(array(
                $formId => $captchaValue,
            )));
        $response->expects($this->once())
            ->method('setRedirect')
            ->with($redirectUrl, 302);
        $controller->expects($this->any())->method('getRequest')->will($this->returnValue($request));
        $controller->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        $this->_captcha->expects($this->any())->method('isRequired')->will($this->returnValue(true));
        $this->_captcha->expects($this->once())
            ->method('isCorrect')
            ->with($captchaValue)
            ->will($this->returnValue(false));
        $this->_helper->expects($this->any())->method('getCaptcha')
            ->with($formId)
            ->will($this->returnValue($this->_captcha));
        $this->_session->expects($this->once())->method('addError')->with($warningMessage);
        $controller->expects($this->once())->method('setFlag')
            ->with('', Magento_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);

        $this->_observer->checkContactUsForm(new Magento_Event_Observer(array('controller_action' => $controller)));
    }

    public function testCheckContactUsFormDoesNotCheckCaptchaWhenItIsNotRequired()
    {
        $this->_helper->expects($this->any())->method('getCaptcha')
            ->with('contact_us')
            ->will($this->returnValue($this->_captcha));
        $this->_captcha->expects($this->any())->method('isRequired')->will($this->returnValue(false));
        $this->_captcha->expects($this->never())->method('isCorrect');

        $this->_observer->checkContactUsForm(new Magento_Event_Observer());
    }

    /**
     * Get stub for resource model
     * @return Magento_Captcha_Model_Resource_Log
     */
    protected function _getResourceModelStub()
    {
        $resourceModel = $this->getMock('Magento_Captcha_Model_Resource_Log',
            array('deleteUserAttempts', 'deleteOldAttempts'), array(), '', false);

        return $resourceModel;
    }
}
