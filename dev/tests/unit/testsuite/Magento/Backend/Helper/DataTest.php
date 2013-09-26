<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Helper_Data
     */
    protected $_helper;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var Magento_Core_Model_Config_Primary
     */
    protected $_primaryConfigMock;

    protected function setUp()
    {
        $this->_configMock = $this->getMock('Magento_Core_Model_Config', array(), array(), '', false, false);
        $this->_primaryConfigMock =
            $this->getMock('Magento_Core_Model_Config_Primary', array(), array(), '', false, false);
        $appMock = $this->getMock('Magento_Core_Model_App', array(), array(),
            'Magento_Core_Model_AppProxy', false, false);
        $backendUrlMock = $this->getMock('Magento_Backend_Model_Url', array(), array(),
            'Magento_Backend_Model_UrlProxy', false, false);
        $authMock = $this->getMock('Magento_Backend_Model_Auth', array(), array(),
            'Magento_Backend_Model_AuthProxy', false, false);
        $helper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_helper = $helper->getObject('Magento_Backend_Helper_Data', array(
            'applicationConfig'    => $this->_configMock,
            'primaryConfig'        => $this->_primaryConfigMock,
            'app'                  => $appMock,
            'backendUrl'           => $backendUrlMock,
            'auth'                 => $authMock,
            'defaultAreaFrontName' => 'backend'
        ));
    }

    public function testGetAreaFrontNameReturnsDefaultValueWhenCustomNotSet()
    {
        $this->_configMock->expects($this->once())->method('getValue')
            ->with(Magento_Backend_Helper_Data::XML_PATH_USE_CUSTOM_ADMIN_PATH, 'default')
            ->will($this->returnValue(false));

        $this->_primaryConfigMock->expects($this->once())->method('getNode')
            ->with(Magento_Backend_Helper_Data::XML_PATH_BACKEND_AREA_FRONTNAME)
            ->will($this->returnValue(''));

        $this->assertEquals('backend', $this->_helper->getAreaFrontName());
    }

    public function testGetAreaFrontNameLocalConfigCustomFrontName()
    {
        $this->_configMock->expects($this->once())->method('getValue')
            ->with(Magento_Backend_Helper_Data::XML_PATH_USE_CUSTOM_ADMIN_PATH, 'default')
            ->will($this->returnValue(false));

        $this->_primaryConfigMock->expects($this->once())->method('getNode')
            ->with(Magento_Backend_Helper_Data::XML_PATH_BACKEND_AREA_FRONTNAME)
            ->will($this->returnValue('backend_custom'));

        $this->assertEquals('backend_custom', $this->_helper->getAreaFrontName());
    }

    public function testGetAreaFrontNameAdminConfigCustomFrontName()
    {
        $this->_configMock->expects($this->at(0))->method('getValue')
            ->with(Magento_Backend_Helper_Data::XML_PATH_USE_CUSTOM_ADMIN_PATH, 'default')
            ->will($this->returnValue(true));

        $this->_configMock->expects($this->at(1))->method('getValue')
            ->with(Magento_Backend_Helper_Data::XML_PATH_CUSTOM_ADMIN_PATH, 'default')
            ->will($this->returnValue('control'));

        $this->assertEquals('control', $this->_helper->getAreaFrontName());
    }

    public function testClearAreaFrontName()
    {
        $this->_primaryConfigMock->expects($this->exactly(2))->method('getNode');
        $this->_configMock->expects($this->exactly(2))->method('getValue');

        $this->_helper->getAreaFrontName();
        $this->_helper->clearAreaFrontName();
        $this->_helper->getAreaFrontName();
    }

    public function testGetAreaFrontNameReturnsValueFromCache()
    {
        $this->_primaryConfigMock->expects($this->once())->method('getNode');
        $this->_configMock->expects($this->once())->method('getValue');
        $this->_helper->getAreaFrontName();
        $this->_helper->getAreaFrontName();
    }
}
