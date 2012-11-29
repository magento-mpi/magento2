<?php
/**
 * Unit Test for Pinba profiler driver.
 *
 * @copyright {}
 */
class Magento_Profiler_Driver_PinbaTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Profiler_Driver_Pinba
     */
    protected $_driver;

    /**
     * @var ReflectionProperty
     */
    protected $_property;

    public static function setUpBeforeClass()
    {
        require_once __DIR__ . '/../_files/pinba_functions.php';
    }

    protected function setUp()
    {
        $this->_driver = new Magento_Profiler_Driver_Pinba();
        $this->_property = new ReflectionProperty('Magento_Profiler_Driver_Pinba', '_startedTimers');
        $this->_property->setAccessible(true);
    }

    protected function tearDown()
    {
        unset($this->_driver);
    }

    public function testStartStop()
    {
        $this->_driver->start('timer1');
        $this->assertCount(1, $this->_property->getValue($this->_driver));

        $this->_driver->start('timer2');
        $this->assertCount(2, $this->_property->getValue($this->_driver));

        $this->_driver->stop('timer1');
        $this->assertCount(1, $this->_property->getValue($this->_driver));

        $this->_driver->stop('timer2');
        $this->assertCount(0, $this->_property->getValue($this->_driver));
    }

    public function testResetSingle()
    {
        $this->_driver->start('timer1');
        $this->_driver->start('timer2');
        $this->assertCount(2, $this->_property->getValue($this->_driver));

        $this->_driver->reset('timer1');
        $this->assertCount(1, $this->_property->getValue($this->_driver));
    }

    public function testResetAll()
    {
        $this->_driver->start('timer1');
        $this->_driver->start('timer2');
        $this->assertCount(2, $this->_property->getValue($this->_driver));

        $this->_driver->reset();
        $this->assertCount(0, $this->_property->getValue($this->_driver));
    }
}