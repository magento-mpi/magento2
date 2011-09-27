<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Profiler
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test case for Magento_Profiler_OutputAbstract
 *
 * @group profiler
 */
class Magento_Profiler_OutputAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Profiler_OutputAbstract|PHPUnit_Framework_MockObject_MockObject
     */
    private $_object;

    /**
     * @var ReflectionMethod
     */
    private $_timersGetter;

    public static function setUpBeforeClass()
    {
        Magento_Profiler::enable();
        /* Profiler measurements fixture */
        $timersProperty = new ReflectionProperty('Magento_Profiler', '_timers');
        $timersProperty->setAccessible(true);
        $timersProperty->setValue(include __DIR__ . '/_files/timers.php');
        $timersProperty->setAccessible(false);
    }

    public static function tearDownAfterClass()
    {
        Magento_Profiler::reset();
    }

    protected function setUp()
    {
        $this->_object = $this->getMockForAbstractClass('Magento_Profiler_OutputAbstract');

        $this->_timersGetter = new ReflectionMethod(get_class($this->_object), '_getTimers');
        $this->_timersGetter->setAccessible(true);
    }

    protected function tearDown()
    {
        $this->_timersGetter->setAccessible(false);
    }

    /**
     * @dataProvider getTimersDataProvider
     */
    public function testGetTimers($filter, $expectedTimers)
    {
        $this->_object->__construct($filter);
        $actualTimers = $this->_timersGetter->invoke($this->_object);
        $this->assertEquals($expectedTimers, $actualTimers);
    }

    public function getTimersDataProvider()
    {
        return array(
            'null filter' => array(
                null,
                array(
                    'some_root_timer',
                    'some_root_timer->some_nested_timer',
                    'some_root_timer->some_nested_timer->some_deeply_nested_timer',
                    'one_more_root_timer'
                )
            ),
            'exact timer filter' => array(
                '/^some_root_timer->some_nested_timer$/',
                array(
                    'some_root_timer->some_nested_timer',
                )
            ),
            'timer subtree filter' => array(
                '/^some_root_timer->some_nested_timer/',
                array(
                    'some_root_timer->some_nested_timer',
                    'some_root_timer->some_nested_timer->some_deeply_nested_timer'
                )
            ),
            'timer strict subtree filter' => array(
                '/^some_root_timer->some_nested_timer->/',
                array(
                    'some_root_timer->some_nested_timer->some_deeply_nested_timer'
                )
            ),
        );
    }

    /**
     * @dataProvider thresholdDataProvider
     */
    public function testSetThreshold(array $thresholds, $expectedTimers)
    {
        $this->_resetThresholds($this->_object);
        foreach ($thresholds as $fetchKey => $thresholdValue) {
            $this->_object->setThreshold($fetchKey, $thresholdValue);
        }
        $actualTimers = $this->_timersGetter->invoke($this->_object);
        $this->assertEquals($expectedTimers, $actualTimers);
    }

    public function thresholdDataProvider()
    {
        return array(
            'empty' => array(
                array(),
                array(
                    'some_root_timer',
                    'some_root_timer->some_nested_timer',
                    'some_root_timer->some_nested_timer->some_deeply_nested_timer',
                    'one_more_root_timer'
                ),
            ),
            'time' => array(
                array(
                    Magento_Profiler::FETCH_TIME => 0.06,
                ),
                array(
                    'some_root_timer',
                    'some_root_timer->some_nested_timer',
                ),
            ),
            'avg' => array(
                array(
                    Magento_Profiler::FETCH_AVG => 0.038,
                ),
                array(
                    'some_root_timer',
                ),
            ),
            'count' => array(
                array(
                    Magento_Profiler::FETCH_COUNT => 3,
                ),
                array(
                    'some_root_timer->some_nested_timer',
                    'some_root_timer->some_nested_timer->some_deeply_nested_timer',
                ),
            ),
            'avg & count' => array(
                array(
                    Magento_Profiler::FETCH_AVG   => 0.038,
                    Magento_Profiler::FETCH_COUNT => 3,
                ),
                array(
                    'some_root_timer',
                    'some_root_timer->some_nested_timer',
                    'some_root_timer->some_nested_timer->some_deeply_nested_timer',
                ),
            ),
        );
    }

    public function testSetThresholdReset()
    {
        $this->_resetThresholds($this->_object);

        $this->_object->setThreshold(Magento_Profiler::FETCH_COUNT, 4);
        $actualTimers = $this->_timersGetter->invoke($this->_object);
        $this->assertEmpty($actualTimers);

        $this->_object->setThreshold(Magento_Profiler::FETCH_COUNT, null);
        $actualTimers = $this->_timersGetter->invoke($this->_object);
        $expectedTimers = $this->thresholdDataProvider();
        $expectedTimers = $expectedTimers['empty'][1];
        $this->assertEquals($expectedTimers, $actualTimers);
    }

    protected function _resetThresholds(Magento_Profiler_OutputAbstract $output)
    {
        $fetchKeys = array(
            Magento_Profiler::FETCH_TIME,
            Magento_Profiler::FETCH_AVG,
            Magento_Profiler::FETCH_COUNT,
            Magento_Profiler::FETCH_EMALLOC,
            Magento_Profiler::FETCH_REALMEM,
        );
        foreach ($fetchKeys as $fetchKey) {
            $output->setThreshold($fetchKey, null);
        }
    }
}
