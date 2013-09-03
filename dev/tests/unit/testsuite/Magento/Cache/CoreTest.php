<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * \Magento\Cache\Core test case
 */
class Magento_Cache_CoreTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Cache\Core
     */
    protected $_core;

    /**
     * @var array
     */
    protected static $_cacheStorage = array();

    /**
     * Selected mock of Zend_Cache_Backend_File to have extended
     * Zend_Cache_Backend and implemented Zend_Cache_Backend_Interface
     *
     * @var Zend_Cache_Backend_File
     */
    protected $_mockBackend;

    protected function setUp()
    {
        $this->_mockBackend = $this->getMock('Zend_Cache_Backend_File');
    }

    protected function tearDown()
    {
        unset($this->_mockBackend);
    }

    public function testSetBackendDefault()
    {
        $core = new \Magento\Cache\Core();
        $core->setBackend($this->_mockBackend);

        $this->assertNotInstanceOf('\Magento\Cache\Backend\Decorator\DecoratorAbstract', $core->getBackend());
        $this->assertEquals($this->_mockBackend, $core->getBackend());
    }

    /**
     * @dataProvider setBackendExceptionProvider
     * @expectedException Zend_Cache_Exception
     */
    public function testSetBackendException($decorators)
    {
        $core = new \Magento\Cache\Core(array('backend_decorators' => $decorators));
        $core->setBackend($this->_mockBackend);
    }

    public function setBackendExceptionProvider()
    {
        return array(
            'string' => array('string'),
            'decorator setting is not an array' => array(array('decorator' => 'string')),
            'decorator setting is empty array' => array(array('decorator' => array())),
            'no class index in array' => array(array('decorator' => array('somedata'))),
            'non-existing class passed' => array(array('decorator' => array('class' => 'NonExistingClass'))),
        );
    }

    public function testSaveDisabled()
    {
        $backendMock = $this->getMock('Zend_Cache_Backend_BlackHole');
        $backendMock->expects($this->never())
            ->method('save');
        $frontend = new \Magento\Cache\Core(array('disable_save' => true));
        $frontend->setBackend($backendMock);
        $result = $frontend->save('data', 'id');
        $this->assertTrue($result);
    }
}
