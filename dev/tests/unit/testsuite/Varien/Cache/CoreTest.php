<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Varien_Cache_Core test case
 */
class Varien_Cache_CoreTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Varien_Cache_Core
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
        $core = new Varien_Cache_Core();
        $core->setBackend($this->_mockBackend);

        $this->assertNotInstanceOf('Magento_Cache_Backend_Decorator_DecoratorAbstract', $core->getBackend());
        $this->assertEquals($this->_mockBackend, $core->getBackend());
    }

    /**
     * @dataProvider setBackendExceptionProvider
     * @expectedException Zend_Cache_Exception
     */
    public function testSetBackendException($decorators)
    {
        $core = new Varien_Cache_Core(array('backend_decorators' => $decorators));
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
        $frontend = $this->getMock('Varien_Cache_Core', array('_tags'), array(array('disable_save' => true)));
        $frontend->expects($this->never())
            ->method('_tags');
        $result = $frontend->save('data', 'id');
        $this->assertTrue($result);
    }
}
