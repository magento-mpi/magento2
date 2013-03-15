<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once 'Mage/Launcher/Controller/BasePage.php';

abstract class Mage_Launcher_Controller_BasePageTestCaseAbstract extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Launcher_Controller_BasePage|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_controller;

    /**
     * Retrieve mocked page controller instance
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Layout_Factory $layoutFactory
     * @param string|null $areaCode
     * @param array $invokeArgs
     * @return Mage_Launcher_Controller_BasePage
     */
    abstract protected function _getMockedPageControllerInstance(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Response_Http $response,
        Magento_ObjectManager $objectManager,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Layout_Factory $layoutFactory,
        $areaCode = null,
        array $invokeArgs = array()
    );

    protected function setUp()
    {
        $request = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
        $response = $this->getMock('Mage_Core_Controller_Response_Http', array(), array(), '', false);
        $objectManager = $this->getMock('Magento_ObjectManager', array(), array(), '', false);
        $frontController = $this->getMock('Mage_Core_Controller_Varien_Front', array(), array(), '', false);
        $layoutFactory = $this->getMock('Mage_Core_Model_Layout_Factory', array(), array(), '', false);
        $backendHelper = $this->getMock('Mage_Backend_Helper_Data', array(), array(), '', false);
        $backendSession = $this->getMock('Mage_Backend_Model_Session', array(), array(), '', false);

        $this->_controller = $this->_getMockedPageControllerInstance(
            $request,
            $response,
            $objectManager,
            $frontController,
            $layoutFactory,
            null,
            array('helper' => $backendHelper, 'session' => $backendSession)
        );
    }

    protected function tearDown()
    {
        $this->_controller = null;
    }

    public function testIndexAction()
    {
        $this->_controller->expects($this->once())->method('loadLayout')->will($this->returnSelf());
        $this->_controller->expects($this->once())->method('getLayout')->will($this->returnValue(null));
        $this->_controller->expects($this->once())->method('renderLayout')->will($this->returnSelf());
        $this->_controller->expects($this->once())
            ->method('_setActiveMenu')
            ->with($this->equalTo('Mage_Adminhtml::dashboard'))
            ->will($this->returnSelf());

        $this->_controller->indexAction();
    }
}
