<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_TestFramework_Bootstrap_Memory.
 */
class Magento_Test_Bootstrap_MemoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_Bootstrap_Memory
     */
    protected $_object;

    /**
     * @var Magento_TestFramework_MemoryLimit|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_memoryLimit;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_activationPolicy;

    protected function setUp()
    {
        $this->_memoryLimit = $this->getMock('Magento_TestFramework_MemoryLimit', array('printStats'), array(), '', false);
        $this->_activationPolicy = $this->getMock('stdClass', array('register_shutdown_function'));
        $this->_object = new Magento_TestFramework_Bootstrap_Memory(
            $this->_memoryLimit, array($this->_activationPolicy, 'register_shutdown_function')
        );
    }

    protected function tearDown()
    {
        $this->_memoryLimit = null;
        $this->_activationPolicy = null;
        $this->_object = null;
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Activation policy is expected to be a callable.
     */
    public function testConstructorException()
    {
        new Magento_TestFramework_Bootstrap_Memory($this->_memoryLimit, 'non_existing_callable');
    }

    public function testDisplayStats()
    {
        $eol = PHP_EOL;
        $this->expectOutputString("{$eol}=== Memory Usage System Stats ==={$eol}Dummy Statistics{$eol}");
        $this->_memoryLimit
            ->expects($this->once())
            ->method('printStats')
            ->will($this->returnValue('Dummy Statistics'))
        ;
        $this->_object->displayStats();
    }

    public function testActivateStatsDisplaying()
    {
        $this->_activationPolicy
            ->expects($this->once())
            ->method('register_shutdown_function')
            ->with($this->identicalTo(array($this->_object, 'displayStats')))
        ;
        $this->_object->activateStatsDisplaying();
    }

    public function testActivateLimitValidation()
    {
        $this->_activationPolicy
            ->expects($this->once())
            ->method('register_shutdown_function')
            ->with($this->identicalTo(array($this->_memoryLimit, 'validateUsage')))
        ;
        $this->_object->activateLimitValidation();
    }
}
