<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Newsletter_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_saasHelper;

    /**
     * @var Saas_Newsletter_Model_Observer
     */
    protected $_observerMock;

    /**
     * @var Saas_Newsletter_Model_Observer
     */
    protected $_modelNewsletterObserver;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
        $this->_saasHelper = $this->getMock('Saas_Saas_Helper_Data', array(), array(), '', false);
        $this->_observerMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_modelNewsletterObserver = $objectManagerHelper->getObject('Saas_Newsletter_Model_Observer', array(
            'request' => $this->_requestMock,
            'saasHelper' => $this->_saasHelper,
        ));
    }

    public function testLimitNewsletterFunctionality()
    {
        $this->_requestMock->expects($this->any())->method('getControllerModule')
            ->will($this->returnValue('Mage_Adminhtml'));
        $this->_requestMock->expects($this->any())->method('getControllerName')
            ->will($this->returnValue('newsletter_controller'));
        $this->_saasHelper->expects($this->once())->method('customizeNoRoutForward');

        $this->_modelNewsletterObserver->limitNewsletterFunctionality($this->_observerMock);
    }

    /**
     * @param string $module
     * @param string $controller
     * @dataProvider dataProviderForNotLimitNewsletterFunctionality
     */
    public function testNotLimitNewsletterFunctionality($module, $controller)
    {
        $this->_requestMock->expects($this->any())->method('getControllerModule')->will($this->returnValue($module));
        $this->_requestMock->expects($this->any())->method('getControllerName')->will($this->returnValue($controller));
        $this->_saasHelper->expects($this->never())->method('customizeNoRoutForward');

        $this->_modelNewsletterObserver->limitNewsletterFunctionality($this->_observerMock);
    }

    /**
     * @return array
     */
    public function dataProviderForNotLimitNewsletterFunctionality()
    {
        return array(
            array('Mage_Adminhtml', 'newsletter_subscriber'), // the allowed controller
            array('Mage_Adminhtml', 'unknown'),               // a controller without newsletter functionality
            array('unknown', 'newsletter_subscriber'),        // a controller with the same name in other module
            array('unknown', 'unknown')                       // an another module
        );
    }
}
