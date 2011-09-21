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
    private $_output;

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
    }

    public static function tearDownAfterClass()
    {
        Magento_Profiler::reset();
    }

    protected function setUp()
    {
        $this->_output = $this->getMockForAbstractClass('Magento_Profiler_OutputAbstract');

        $this->_timersGetter = new ReflectionMethod(get_class($this->_output), '_getTimers');
        $this->_timersGetter->setAccessible(true);
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

    public function filterDataProvider()
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
            'exact timer' => array(
                '/^some_root_timer->some_nested_timer$/',
                array(
                    'some_root_timer->some_nested_timer',
                )
            ),
            'timer subtree' => array(
                '/^some_root_timer->some_nested_timer/',
                array(
                    'some_root_timer->some_nested_timer',
                    'some_root_timer->some_nested_timer->some_deeply_nested_timer'
                )
            ),
            'timer strict subtree' => array(
                '/^some_root_timer->some_nested_timer->/',
                array(
                    'some_root_timer->some_nested_timer->some_deeply_nested_timer'
                )
            ),
        );
    }

    /**
     * @dataProvider filterDataProvider
     */
    public function testFilter($filter, $expectedTimers)
    {
        $this->_output = $this->getMockForAbstractClass('Magento_Profiler_OutputAbstract', array($filter));

        $timerGetter = new ReflectionMethod(get_class($this->_output), '_getTimers');
        $timerGetter->setAccessible(true);

        $actualTimers = $timerGetter->invoke($this->_output);
        $this->assertEquals($expectedTimers, $actualTimers);
    }

    public function testFilterDefaults()
    {
        $actualTimers = $this->_timersGetter->invoke($this->_output);

        $expectedTimers = self::filterDataProvider();
        $expectedTimers = $expectedTimers['null filter'][1];

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

    /**
     * @dataProvider thresholdDataProvider
     */
    public function testSetThreshold(array $thresholds, $expectedTimers)
    {
        $this->_resetThresholds($this->_output);
        foreach ($thresholds as $fetchKey => $thresholdValue) {
            $this->_output->setThreshold($fetchKey, $thresholdValue);
        }
        $actualTimers = $this->_timersGetter->invoke($this->_output);
        $this->assertEquals($expectedTimers, $actualTimers);
    }

    public function testResetThreshold()
    {
        $this->_resetThresholds($this->_output);

        $this->_output->setThreshold(Magento_Profiler::FETCH_COUNT, 4);
        $actualTimers = $this->_timersGetter->invoke($this->_output);
        $this->assertEmpty($actualTimers);

        $this->_output->setThreshold(Magento_Profiler::FETCH_COUNT, null);
        $actualTimers = $this->_timersGetter->invoke($this->_output);
        $expectedTimers = $this->thresholdDataProvider();
        $expectedTimers = $expectedTimers['empty'][1];
        $this->assertEquals($expectedTimers, $actualTimers);
    }
}
