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

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_areaList;

    protected function setUp()
    {
        $this->_readerMock = $this->getMock('Magento\App\Route\Config\Reader', array(), array(), '', false);
        $this->_cacheMock = $this->getMock('Magento\Config\CacheInterface');
        $this->_configScopeMock = $this->getMock('\Magento\Config\ScopeInterface');
        $this->_areaList = $this->getMock('\Magento\App\AreaList', array(), array(), '', false);
        $this->_configScopeMock->expects(
            $this->any()
        )->method(
            'getCurrentScope'
        )->will(
            $this->returnValue('areaCode')
        );
        $this->_config = new \Magento\App\Route\Config(
            $this->_readerMock,
            $this->_cacheMock,
            $this->_configScopeMock,
            $this->_areaList
        );
    }

    public function testGetRouteFrontNameIfCacheIfRouterIdNotExist()
    {
        $this->_cacheMock->expects(
            $this->once()
        )->method(
            'load'
        )->with(
            'areaCode::RoutesConfig'
        )->will(
            $this->returnValue(serialize(array('expected')))
        );
        $this->assertEquals('routerCode', $this->_config->getRouteFrontName('routerCode'));
    }

    public function testGetRouteByFrontName()
    {
        $this->_cacheMock->expects(
            $this->once()
        )->method(
            'load'
        )->with(
            'areaCode::RoutesConfig'
        )->will(
            $this->returnValue(serialize(array('routerCode' => ['frontName' => 'routerName'])))
        );
        $this->assertEquals('routerCode', $this->_config->getRouteByFrontName('routerName'));
    }

    public function testGetModulesByFrontName()
    {
        $this->_cacheMock->expects(
            $this->once()
        )->method(
            'load'
        )->with(
            'areaCode::RoutesConfig'
        )->will(
            $this->returnValue(
                serialize(array('routerCode' => ['frontName' => 'routerName', 'modules' => ['Module1']]))
            )
        );
        $this->assertEquals(['Module1'], $this->_config->getModulesByFrontName('routerName'));
    }
}
