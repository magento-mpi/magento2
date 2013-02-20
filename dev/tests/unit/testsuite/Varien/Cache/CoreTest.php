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

    protected function setUp()
    {
        $this->_core = new Varien_Cache_Core();
    }

    protected function tearDown()
    {
        unset ($this->_core);
    }

    public function testDecorateBackend()
    {
        $this->_core->setOption('compression', false);
        $this->_core->setBackend(new Zend_Cache_Backend());
        $this->_core->decorateBackend();

        $this->assertNotInstanceOf('Varien_Cache_Backend_Decorator', $this->_core->getBackend());
    }

}
