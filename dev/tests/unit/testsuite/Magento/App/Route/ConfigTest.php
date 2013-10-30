<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Route;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Route\Config
     */
    protected $_config;

    /**
     * @var Cache_Mock_Wrapper
     */
    protected $_readerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configScopeMock;

    protected function setUp()
    {
        $this->_readerMock = $this->getMock('Magento\App\Route\Config\Reader', array(), array(), '', false);
        $this->_cacheMock = $this->getMock('Magento\Config\CacheInterface');
        $this->_configScopeMock = $this->getMock('\Magento\Config\ScopeInterface');
        $this->_configScopeMock
            ->expects($this->any())
            ->method('getCurrentScope')
            ->will($this->returnValue('areaCode'));
        $this->_config = new \Magento\App\Route\Config(
            $this->_readerMock,
            $this->_cacheMock,
            $this->_configScopeMock
        );
    }

    public function testGetIfCacheIsArray()
    {
        $this->_cacheMock->expects($this->once())
            ->method('load')->with('areaCode::RoutesConfig-routerCode')
            ->will($this->returnValue(serialize(array('expected'))));
        $this->assertEquals(array('expected'), $this->_config->getRoutes('routerCode'));
    }

    public function testGetIfKeyExist()
    {
        $this->_readerMock->expects($this->once())
            ->method('read')->with('areaCode')->will($this->returnValue(array()));
        $this->assertEquals(array(), $this->_config->getRoutes('routerCode'));
    }

    public function testGetRoutes()
    {
        $areaConfig['routerCode']['routes'] = 'Expected Value';
        $this->_readerMock->expects($this->once())
            ->method('read')->with('areaCode')->will($this->returnValue($areaConfig));
        $this->_cacheMock->expects($this->once())
            ->method('save')->with(serialize('Expected Value'), 'areaCode::RoutesConfig-routerCode');
        $this->assertEquals('Expected Value', $this->_config->getRoutes('routerCode'));
    }
}
