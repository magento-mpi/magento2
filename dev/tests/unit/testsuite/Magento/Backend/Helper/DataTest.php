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

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_frontResolverMock;

    protected function setUp()
    {
        $this->_configMock = $this->getMock('Magento\Core\Model\Config', array(), array(), '', false, false);
        $this->_primaryConfigMock =
            $this->getMock('Magento\Core\Model\Config\Primary', array(), array(), '', false, false);
        $this->_frontResolverMock
            = $this->getMock('\Magento\Backend\App\Area\FrontNameResolver', array(), array(), '', false);
        $this->_helper = new \Magento\Backend\Helper\Data(
            $this->getMock('Magento\Core\Helper\Context', array(), array(), '', false, false),
            $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false, false),
            $this->_primaryConfigMock,
            $this->getMock('Magento\App\RouterList', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\App', array(), array(), '', false),
            $this->getMock('\Magento\Backend\Model\Url', array(), array(), '', false),
            $this->getMock('\Magento\Backend\Model\Auth', array(), array(), '', false),
            $this->_frontResolverMock
        );
    }

    public function testGetAreaFrontNameLocalConfigCustomFrontName()
    {
        $this->_frontResolverMock->expects($this->once())
            ->method('getFrontName')
            ->will($this->returnValue('custom_backend'));

        $this->assertEquals('custom_backend', $this->_helper->getAreaFrontName());
    }
}
