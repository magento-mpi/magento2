<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\ObjectManager;
class ConfigCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Magento\App\ObjectManager\ConfigCache
     */
    protected $_configCache;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheFrontendMock;

    protected function setUp()
    {
        $this->_cacheFrontendMock = $this->getMock('\Magento\Cache\FrontendInterface');
        $this->_configCache = new \Magento\App\ObjectManager\ConfigCache($this->_cacheFrontendMock);
    }

    protected function tearDown()
    {
        unset($this->_configCache);
    }

    public function testGet()
    {
        $key = 'key';
        $this->_cacheFrontendMock
            ->expects($this->once())
            ->method('load')
            ->with('diConfig' . $key)
            ->will($this->returnValue(false));
        $this->assertEquals(false, $this->_configCache->get($key));
    }

    public function testSave()
    {
        $key = 'key';
        $config = array('config');
        $this->_cacheFrontendMock->expects($this->once())->method('save')->with(serialize($config), 'diConfig' . $key);
        $this->_configCache->save($config, $key);
    }
}
