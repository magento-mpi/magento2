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

namespace Magento\Backend\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_helper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var \Magento\Core\Model\Config\Primary
     */
    protected $_primaryConfigMock;

    protected function setUp()
    {
        $this->_configMock = $this->getMock('Magento\Core\Model\Config', array(), array(), '', false, false);
        $this->_primaryConfigMock =
            $this->getMock('Magento\Core\Model\Config\Primary', array(), array(), '', false, false);

        $this->_helper = new \Magento\Backend\Helper\Data(
            $this->getMock('Magento\Core\Helper\Context', array(), array(), '', false, false),
            $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false, false),
            $this->_configMock,
            $this->_primaryConfigMock,
            $this->getMock('Magento\App\RouterList', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\App', array(), array(), '', false),
            $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false),
            $this->getMock('Magento\Backend\Model\Auth', array(), array(), '', false),
            'backend',
            'custom_backend'
        );
    }

    public function testGetAreaFrontNameReturnsDefaultValueWhenCustomNotSet()
    {
        $this->_helper = new \Magento\Backend\Helper\Data(
            $this->getMock('Magento\Core\Helper\Context', array(), array(), '', false, false),
            $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false, false),
            $this->_configMock,
            $this->_primaryConfigMock,
            $this->getMock('Magento\App\RouterList', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\App', array(), array(), '', false),
            $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false),
            $this->getMock('Magento\Backend\Model\Auth', array(), array(), '', false),
            'backend',
            ''
        );

        $this->_configMock->expects($this->once())->method('getValue')
            ->with(\Magento\Backend\Helper\Data::XML_PATH_USE_CUSTOM_ADMIN_PATH, 'default')
            ->will($this->returnValue(false));

        $this->assertEquals('backend', $this->_helper->getAreaFrontName());
    }

    public function testGetAreaFrontNameLocalConfigCustomFrontName()
    {
        $this->_configMock->expects($this->once())->method('getValue')
            ->with(\Magento\Backend\Helper\Data::XML_PATH_USE_CUSTOM_ADMIN_PATH, 'default')
            ->will($this->returnValue(false));

        $this->assertEquals('custom_backend', $this->_helper->getAreaFrontName());
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
        $this->_configMock->expects($this->exactly(2))->method('getValue');

        $this->_helper->getAreaFrontName();
        $this->_helper->clearAreaFrontName();
        $this->_helper->getAreaFrontName();
    }

    public function testGetAreaFrontNameReturnsValueFromCache()
    {
        $this->_configMock->expects($this->once())->method('getValue');
        $this->_helper->getAreaFrontName();
        $this->_helper->getAreaFrontName();
    }
}
