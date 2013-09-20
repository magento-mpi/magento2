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
 * Test class for \Magento\TestFramework\Bootstrap\Profiler.
 */
namespace Magento\Test\Bootstrap;

class ProfilerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Bootstrap\Profiler
     */
    protected $_object;

    /**
     * @var \Magento\Profiler\Driver\Standard|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_driver;

    protected function setUp()
    {
        $this->expectOutputString('');
        $this->_driver = $this->getMock('Magento\Profiler\Driver\Standard', array('registerOutput'));
        $this->_object = new \Magento\TestFramework\Bootstrap\Profiler($this->_driver);
    }

    protected function tearDown()
    {
        $this->_driver = null;
        $this->_object = null;
    }

    public function testRegisterFileProfiler()
    {
        $this->_driver
            ->expects($this->once())
            ->method('registerOutput')
            ->with($this->isInstanceOf('Magento\Profiler\Driver\Standard\Output\Csvfile'))
        ;
        $this->_object->registerFileProfiler('php://output');
    }

    public function testRegisterBambooProfiler()
    {
        $this->_driver
            ->expects($this->once())
            ->method('registerOutput')
            ->with($this->isInstanceOf('Magento\TestFramework\Profiler\OutputBamboo'))
        ;
        $this->_object->registerBambooProfiler('php://output', __DIR__ . '/_files/metrics.php');
    }
}
