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
     * Backup value of Magento_Profiler enable state
     *
     * @var bool
     */
    protected $_profilerEnabled;

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
        $this->_profilerEnabled = Magento_Profiler::isEnabled();
        $this->_stat = $this->getMock('Magento_Profiler_Driver_Standard_Stat');
        $this->_driver = new Magento_Profiler_Driver_Standard($this->_stat);
    }

    protected function tearDown()
    {
        if (Magento_Profiler::isEnabled() && !$this->_profilerEnabled) {
            Magento_Profiler::enable();
        } elseif (Magento_Profiler::isEnabled()) {
            Magento_Profiler::disable();
        }
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
     * Test reset method
     */
    public function testReset()
    {
        $this->_stat->expects($this->once())->method('reset')->with('timer_name');
        $this->_driver->reset('timer_name');
    }

    /**
     * Test start method
     */
    public function testStart()
    {
        $this->_stat->expects($this->once())->method('start')->with(
            'timer_name',
            $this->greaterThanOrEqual(microtime(true)),
            $this->greaterThanOrEqual(0),
            $this->greaterThanOrEqual(0)
        );
        $this->_driver->start('timer_name');
    }

    /**
     * Test stop method
     */
    public function testStop()
    {
        $this->_stat->expects($this->once())->method('stop')->with(
            'timer_name',
            $this->greaterThanOrEqual(microtime(true)),
            $this->greaterThanOrEqual(0),
            $this->greaterThanOrEqual(0)
        );
        $this->_driver->stop('timer_name');
    }
}
