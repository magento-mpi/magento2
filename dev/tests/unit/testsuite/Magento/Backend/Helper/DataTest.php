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
     * @var \Magento\Backend\Helper\Data
     */
    protected $_helper;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var \Magento\Core\Model\Config\Primary
     */
    protected $_primaryConfigMock;

    public function setUp()
    {
        $this->_configMock = $this->getMock('Magento\Core\Model\Config', array(), array(), '', false, false);
        $this->_primaryConfigMock =
            $this->getMock('Magento\Core\Model\Config\Primary', array(), array(), '', false, false);

        $this->_helper = new \Magento\Backend\Helper\Data($this->_configMock,
            $this->_primaryConfigMock,
            $this->getMock('Magento\Core\Helper\Context', array(), array(), '', false, false),
            $this->getMock('Magento\Core\Model\RouterList', array(), array(), '', false),
            'backend'
        );
    }

    public function testGetAreaFrontNameReturnsDefaultValueWhenCustomNotSet()
    {
        $this->_configMock->expects($this->once())->method('getValue')
            ->with(\Magento\Backend\Helper\Data::XML_PATH_USE_CUSTOM_ADMIN_PATH, 'default')
            ->will($this->returnValue(false));

        $this->_primaryConfigMock->expects($this->once())->method('getNode')
            ->with(\Magento\Backend\Helper\Data::XML_PATH_BACKEND_AREA_FRONTNAME)
            ->will($this->returnValue(''));

        $this->assertEquals('backend', $this->_helper->getAreaFrontName());
    }

    public function testGetAreaFrontNameLocalConfigCustomFrontName()
    {
        $this->_configMock->expects($this->once())->method('getValue')
            ->with(\Magento\Backend\Helper\Data::XML_PATH_USE_CUSTOM_ADMIN_PATH, 'default')
            ->will($this->returnValue(false));

        $this->_primaryConfigMock->expects($this->once())->method('getNode')
            ->with(\Magento\Backend\Helper\Data::XML_PATH_BACKEND_AREA_FRONTNAME)
            ->will($this->returnValue('backend_custom'));

        $this->assertEquals('backend_custom', $this->_helper->getAreaFrontName());
    }

    public function testGetAreaFrontNameAdminConfigCustomFrontName()
    {
        $this->_configMock->expects($this->at(0))->method('getValue')
            ->with(\Magento\Backend\Helper\Data::XML_PATH_USE_CUSTOM_ADMIN_PATH, 'default')
            ->will($this->returnValue(true));

        $this->_configMock->expects($this->at(1))->method('getValue')
            ->with(\Magento\Backend\Helper\Data::XML_PATH_CUSTOM_ADMIN_PATH, 'default')
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
