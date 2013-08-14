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
class Saas_Launcher_Controller_Adminhtml_HubTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Launcher_Controller_Adminhtml_Hub|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_controller;

    /**
     * @var Saas_Launcher_Helper_Data
     */
    protected $_helperLauncherMock;

    /**
     * @var Magento_Core_Controller_Response_Http
     */
    protected $_responseMock;

    public function setUp()
    {
        $this->_responseMock = $this->getMock('Magento_Core_Controller_Response_Http', array(), array(), '', false, false);
        $this->_helperLauncherMock = $this->getMock('Saas_Launcher_Helper_Data', array(), array(), '', false, false);
        $helperMock = $this->getMock('Magento_Backend_Helper_Data', array(), array(), '', false, false);
        $helperMock->expects($this->any())->method('getUrl')->will($this->returnArgument(0));

        $helper = new Magento_Test_Helper_ObjectManager($this);
        $arguments = array(
            'response' => $this->_responseMock,
            'helper' => $helperMock,
        );
        $context = $helper->getObject('Magento_Backend_Controller_Context', $arguments);

        $this->_controller = $this->getMock(
            'Saas_Launcher_Controller_Adminhtml_Hub', array('getLayout'),
            array(
                $context,
                $this->_helperLauncherMock,
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
            array(Saas_Launcher_Helper_Data::LAUNCHER_PHASE_PROMOTE_STORE, '*/promotestore_index/index'),
            array(Saas_Launcher_Helper_Data::LAUNCHER_PHASE_STORE_LAUNCHER, '*/storelauncher_index/index'),
        );
    }
}
