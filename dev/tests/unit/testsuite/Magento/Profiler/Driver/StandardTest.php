<?php
/**
 * Test class for Magento_Profiler_Driver_Standard
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_Driver_StandardTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Profiler_Driver_Standard_Stat|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_stat;

    /**
     * @var Magento_Profiler_Driver_Standard
     */
    protected $_driver;

    protected function setUp()
    {
        $this->_stat = $this->getMock('Magento_Profiler_Driver_Standard_Stat');
        $this->_driver = new Magento_Profiler_Driver_Standard($this->_stat);
    }

    protected function tearDown()
    {
        Magento_Profiler::reset();
    }

    /**
     * Test __construct method
     */
    public function testConstructor()
    {
        $this->assertAttributeEquals($this->_stat, '_stat', $this->_driver);
        $this->assertAttributeEquals($this->_stat, '_stat', $this->_driver);
        $this->_driver = new Magento_Profiler_Driver_Standard();
        $this->assertAttributeInstanceOf('Magento_Profiler_Driver_Standard_Stat', '_stat', $this->_driver);
    }

    /**
     * Test display method
     */
    public function testDisplayAndRegisterOutput()
    {
        $outputOne = $this->getMock('Magento_Profiler_Driver_Standard_OutputInterface');
        $outputOne->expects($this->once())->method('display')->with($this->_stat);
        $outputTwo = $this->getMock('Magento_Profiler_Driver_Standard_OutputInterface');
        $outputTwo->expects($this->once())->method('display')->with($this->_stat);

        $this->_driver->registerOutput($outputOne);
        $this->_driver->registerOutput($outputTwo);
        Magento_Profiler::enable();
        $this->_driver->display();
        Magento_Profiler::disable();
        $this->_driver->display();
    }

    /**
     * Test clear method
     */
    public function testClear()
    {
        $this->_stat->expects($this->once())->method('clear')->with('timer_id');
        $this->_driver->clear('timer_id');
    }

    /**
     * Test start method
     */
    public function testStart()
    {
        $this->_stat->expects($this->once())->method('start')->with(
            'timer_id',
            $this->greaterThanOrEqual(microtime(true)),
            $this->greaterThanOrEqual(0),
            $this->greaterThanOrEqual(0)
        );
        $this->_driver->start('timer_id');
    }

    /**
     * Test stop method
     */
    public function testStop()
    {
        $this->_stat->expects($this->once())->method('stop')->with(
            'timer_id',
            $this->greaterThanOrEqual(microtime(true)),
            $this->greaterThanOrEqual(0),
            $this->greaterThanOrEqual(0)
        );
        $this->_driver->stop('timer_id');
    }
}
