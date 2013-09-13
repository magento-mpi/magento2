<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Route_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Route_Config
     */
    protected $_config;

    /**
     * @var Cache_Mock_Wrapper
     */
    protected $_readerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    protected function setUp()
    {
        $this->_readerMock = $this->getMock('Magento_Core_Model_Route_Config_Reader', array(), array(), '', false);
        $this->_cacheMock = $this->getMock('Magento_Config_CacheInterface');
        $this->_config = new Magento_Core_Model_Route_Config(
            $this->_readerMock,
            $this->_cacheMock
        );
    }

    public function testGetIfCacheIsArray()
    {
        $this->_cacheMock->expects($this->once())
            ->method('load')->with('areaCode::RoutesConfig-routerCode')
            ->will($this->returnValue(array('expected')));
        $this->assertEquals(array('expected'), $this->_config->getRoutes('areaCode', 'routerCode'));
    }

    public function testGetIfKeyExist()
    {
        $this->_readerMock->expects($this->once())
            ->method('read')->with('areaCode')->will($this->returnValue(array()));
        $this->assertEquals(array(), $this->_config->getRoutes('areaCode', 'routerCode'));
    }

    public function testGetRoutes()
    {
        $areaConfig['routerCode']['routes'] = 'Expected Value';
        $this->_readerMock->expects($this->once())
            ->method('read')->with('areaCode')->will($this->returnValue($areaConfig));
        $this->_cacheMock->expects($this->once())
            ->method('save')->with('Expected Value', 'areaCode::RoutesConfig-routerCode');
        $this->assertEquals('Expected Value', $this->_config->getRoutes('areaCode', 'routerCode'));
    }
}
