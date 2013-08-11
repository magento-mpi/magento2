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
require_once 'Saas/Launcher/controllers/Adminhtml/Storelauncher/IndexController.php';

class Saas_Launcher_Adminhtml_Storelauncher_IndexControllerTest
    extends Saas_Launcher_Controller_BasePageTestCaseAbstract
{
    /**
     * @var Mage_Core_Controller_Response_Http|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_responseMock;

    /**
     * @var Mage_Core_Model_Config|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configModelMock;

    /**
     * @var Mage_Core_Model_Config_Storage_WriterInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configWriterMock;

    /**
     * @var Saas_Launcher_Helper_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_launcherHelperMock;

    /**
     * Retrieve mocked page controller instance
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Layout_Factory $layoutFactory
     * @param array $invokeArgs
     * @return Saas_Launcher_Controller_BasePage
     */
    protected function _getMockedPageControllerInstance(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Response_Http $response,
        Magento_ObjectManager $objectManager,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Layout_Factory $layoutFactory,
        array $invokeArgs = array()
    ) {
        $this->_responseMock = $response;
        $this->_configModelMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_configWriterMock = $this->getMock('Mage_Core_Model_Config_Storage_WriterInterface');
        $this->_launcherHelperMock = $this->getMock('Saas_Launcher_Helper_Data', array(), array(), '', false);
        $helper = new Magento_Test_Helper_ObjectManager($this);

        $arguments = array(
            'request' => $request,
            'response' => $this->_responseMock,
            'objectManager' => $objectManager,
            'frontController' => $frontController,
            'layoutFactory' => $layoutFactory,
        );
        $context = $helper->getObject('Mage_Backend_Controller_Context', $arguments);

        return $this->getMock(
            'Saas_Launcher_Adminhtml_Storelauncher_IndexController',
            array('loadLayout', 'getLayout', 'renderLayout', '_setActiveMenu'),
            array(
                $context,
                $this->_configModelMock,
                $this->_configWriterMock,
                $this->_launcherHelperMock
            )
        );
    }

    public function testLaunchAction()
    {
        $pageMock = $this->getMockBuilder('Saas_Launcher_Model_Page')
            ->setMethods(array('loadByPageCode', 'isComplete'))
            ->disableOriginalConstructor()
            ->getMock();
        $pageMock->expects($this->once())
            ->method('loadByPageCode')
            ->will($this->returnSelf());
        $pageMock->expects($this->once())
            ->method('isComplete')
            ->will($this->returnValue(true));

        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Saas_Launcher_Model_Page')
            ->will($this->returnValue($pageMock));

        $this->_configWriterMock->expects($this->at(0))
            ->method('save')
            ->with($this->equalTo('design/head/demonotice'), $this->equalTo(0));
        $this->_configWriterMock->expects($this->at(1))
            ->method('save')
            ->with($this->equalTo('launcher/store/phase'), $this->equalTo('promote_store'));
        $this->_launcherHelperMock->expects($this->once())
            ->method('jsonEncode')
            ->with(array("success" => true))
            ->will($this->returnValue('{"success":true}'));
        $this->_responseMock->expects($this->once())
            ->method('setBody')
            ->with('{"success":true}');


        $this->_controller->launchAction();
    }
}
