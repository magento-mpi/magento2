<?php
/**
 * Unit Test for Pinba profiler driver.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_Driver_PinbaTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Profiler_Driver_Pinba
     */
    protected $_driver;

    public static function setUpBeforeClass()
    {
        require_once __DIR__ . '/../_files/pinba_functions.php';
    }

    protected function setUp()
    {
        $this->_driver = new Magento_Profiler_Driver_Pinba();
    }

    protected function tearDown()
    {
        unset($this->_driver);
    }

    public function testStartStop()
    {
        $this->_driver->start('timer1');
        $this->assertAttributeCount(1, '_startedTimers', $this->_driver);

        $this->_driver->start('timer2');
        $this->assertAttributeCount(2, '_startedTimers', $this->_driver);

        $this->_driver->stop('timer1');
        $this->assertAttributeCount(1, '_startedTimers', $this->_driver);

        $this->_driver->stop('timer2');
        $this->assertAttributeCount(0, '_startedTimers', $this->_driver);
    }

    public function testClearSingle()
    {
        $this->_driver->start('timer1');
        $this->_driver->start('timer2');
        $this->assertAttributeCount(2, '_startedTimers', $this->_driver);

        $this->_driver->clear('timer1');
        $this->assertAttributeCount(1, '_startedTimers', $this->_driver);
    }

    public function testClearAll()
    {
        $this->_driver->start('timer1');
        $this->_driver->start('timer2');
        $this->assertAttributeCount(2, '_startedTimers', $this->_driver);

        $this->_driver->clear();
        $this->assertAttributeCount(0, '_startedTimers', $this->_driver);
    }
}