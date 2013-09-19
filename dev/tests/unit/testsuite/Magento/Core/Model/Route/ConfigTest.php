<?php
/**
* {license_notice}
*
* @copyright   {copyright}
* @license     {license_link}
*/

namespace Magento\Core\Model\Route;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Route\Config
     */
    protected $_config;

    /**
     * @var Wrapper
     */
    protected $_readerMock;

    /**
     * @var Wrapper
     */
    protected $_cacheMock;

    protected function setUp()
    {
        $this->_readerMock = $this->getMock('Magento\Core\Model\Route\Config\Reader', array(), array(), '', false);
            $this->_cacheMock = new Wrapper();
            $this->_config = new \Magento\Core\Model\Route\Config(
                $this->_readerMock,
                $this->_cacheMock
            );
    }

    public function testGetIfCacheIsArray()
    {
        $this->_cacheMock->getRealMock()->expects($this->once())
        ->method('get')->with('areaCode', 'RoutesConfig-routerCode')
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
        $this->_cacheMock->getRealMock()->expects($this->once())
        ->method('put')->with('Expected Value', 'areaCode', 'RoutesConfig-routerCode');
        $this->assertEquals('Expected Value', $this->_config->getRoutes('areaCode', 'routerCode'));
    }
}
