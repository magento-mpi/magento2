<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once 'Saas/Launcher/Controller/BasePage.php';

abstract class Saas_Launcher_Controller_BasePageTestCaseAbstract extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Launcher_Controller_BasePage|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_controller;

    /**
     * @var Magento_ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * Retrieve mocked page controller instance
     *
     * @param Magento_Core_Controller_Request_Http $request
     * @param Magento_Core_Controller_Response_Http $response
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Controller_Varien_Front $frontController
     * @param Magento_Core_Model_Layout_Factory $layoutFactory
     * @param string|null $areaCode
     * @return Saas_Launcher_Controller_BasePage
     */
    abstract protected function _getMockedPageControllerInstance(
        Magento_Core_Controller_Request_Http $request,
        Magento_Core_Controller_Response_Http $response,
        Magento_ObjectManager $objectManager,
        Magento_Core_Controller_Varien_Front $frontController,
        Magento_Core_Model_Layout_Factory $layoutFactory,
        $areaCode = null
    );

    protected function setUp()
    {
        $request = $this->getMock('Magento_Core_Controller_Request_Http', array(), array(), '', false);
        $response = $this->getMock('Magento_Core_Controller_Response_Http', array(), array(), '', false);
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager', array(), array(), '', false);
        $frontController = $this->getMock('Magento_Core_Controller_Varien_Front', array(), array(), '', false);
        $layoutFactory = $this->getMock('Magento_Core_Model_Layout_Factory', array(), array(), '', false);
        $backendHelper = $this->getMock('Mage_Backend_Helper_Data', array(), array(), '', false);
        $backendSession = $this->getMock('Mage_Backend_Model_Session', array(), array(), '', false);

        $this->_controller = $this->_getMockedPageControllerInstance(
            $request,
            $response,
            $this->_objectManagerMock,
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
            ->with($this->equalTo('Magento_Adminhtml::dashboard'))
            ->will($this->returnSelf());

        $this->_controller->indexAction();
    }
}
