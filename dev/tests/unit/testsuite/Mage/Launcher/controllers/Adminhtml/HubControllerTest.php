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
require_once 'Mage/Launcher/controllers/Adminhtml/HubController.php';

class Mage_Launcher_Adminhtml_HubControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Launcher_Adminhtml_HubController|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_controller;

    /**
     * @var Mage_Launcher_Helper_Data
     */
    protected $_helperLauncherMock;

    /**
     * @var Mage_Core_Controller_Response_Http
     */
    protected $_responseMock;

    public function setUp()
    {
        $requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false, false);
        $this->_responseMock = $this->getMock('Mage_Core_Controller_Response_Http', array(), array(), '', false, false);
        $objectManagerMock = $this->getMock('Magento_ObjectManager', array(), array(), '', false, false);
        $frontControllerMock = $this->getMock('Mage_Core_Controller_Varien_Front', array(), array(), '', false, false);
        $layoutMock = $this->getMock('Mage_Core_Model_Layout_Factory', array(), array(), '', false, false);
        $this->_helperLauncherMock = $this->getMock('Mage_Launcher_Helper_Data', array(), array(), '', false, false);
        $helperMock = $this->getMock('Mage_Backend_Helper_Data', array(), array(), '', false, false);
        $sessionMock = $this->getMock('Mage_Backend_Model_Session', array(), array(), '', false, false);

        $helperMock->expects($this->any())->method('getUrl')->will($this->returnArgument(0));

        $this->_controller = $this->getMock(
            'Mage_Launcher_Adminhtml_HubController', array('getLayout'),
            array(
                $requestMock,
                $this->_responseMock,
                $objectManagerMock,
                $frontControllerMock,
                $layoutMock,
                $this->_helperLauncherMock,
                null,
                array(
                    'helper' => $helperMock,
                    'session' => $sessionMock,
                )
            )
        );
    }

    /**
     * @param $phase
     * @param $redirect
     * @dataProvider indexActionDataProvider
     */
    public function testIndexAction($phase, $redirect)
    {
        $this->_helperLauncherMock->expects($this->once())
            ->method('getLauncherPhase')
            ->will($this->returnValue($phase));
        $this->_responseMock->expects($this->once())->method('setRedirect')->with($redirect);

        $this->_controller->indexAction();
    }

    public function indexActionDataProvider()
    {
        return array(
            array(Mage_Launcher_Helper_Data::LAUNCHER_PHASE_PROMOTE_STORE, '*/promotestore_index/index'),
            array(Mage_Launcher_Helper_Data::LAUNCHER_PHASE_STORE_LAUNCHER, '*/storelauncher_index/index'),
        );
    }
}
