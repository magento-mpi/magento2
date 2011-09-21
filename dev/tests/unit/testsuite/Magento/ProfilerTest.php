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
 * Test case for Magento_Profiler
 *
 * @group profiler
 */
class Magento_ProfilerTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        /* Enable profiler before each test except the test of enable() method itself */
        if ($this->getName(false) == 'testEnable') {
            Magento_Profiler::disable();
            return;
        }
        Magento_Profiler::enable();
        /* Profiler measurements fixture */
        $timersProperty = new ReflectionProperty('Magento_Profiler', '_timers');
        $timersProperty->setAccessible(true);
        $timersProperty->setValue(include __DIR__ . '/Profiler/_files/timers.php');
    }

    protected function tearDown()
    {
        Magento_Profiler::reset();
    }

    public function testGetTimers()
    {
        $expectedTimers = array(
            'some_root_timer',
            'some_root_timer->some_nested_timer',
            'some_root_timer->some_nested_timer->some_deeply_nested_timer',
            'one_more_root_timer'
        );
        $actualTimers = Magento_Profiler::getTimers();
        $this->assertEquals($expectedTimers, $actualTimers);
    }

    public function fetchDataProvider()
    {
        $timerId = 'some_root_timer->some_nested_timer';
        return array(
            'time'    => array($timerId, Magento_Profiler::FETCH_TIME,    0.08),
            'count'   => array($timerId, Magento_Profiler::FETCH_COUNT,   3),
            'emalloc' => array($timerId, Magento_Profiler::FETCH_EMALLOC, 0),
            'realmem' => array($timerId, Magento_Profiler::FETCH_REALMEM, 0),
        );
    }

    /**
     * @dataProvider fetchDataProvider
     */
    public function testFetch($timerId, $fetchKey, $expectedValue)
    {
        $actualValue = Magento_Profiler::fetch($timerId, $fetchKey);
        $this->assertEquals($expectedValue, $actualValue);
    }

    public function testFetchDefaults()
    {
        $timerId = 'some_root_timer->some_nested_timer';
        $expected = Magento_Profiler::fetch($timerId, Magento_Profiler::FETCH_TIME);
        $actual = Magento_Profiler::fetch($timerId);
        $this->assertEquals($expected, $actual);
    }

    public function fetchExceptionDataProvider()
    {
        return array(
            'non-existing timer id'  => array('some_non_existing_timer_id', Magento_Profiler::FETCH_TIME),
            'non-existing fetch key' => array('some_root_timer', 'some_non_existing_fetch_key'),
        );
    }

    /**
     * @dataProvider fetchExceptionDataProvider
     * @expectedException Varien_Exception
     */
    public function testFetchException($timerId, $fetchKey)
    {
        Magento_Profiler::fetch($timerId, $fetchKey);
    }

    public function testDisable()
    {
        Magento_Profiler::start('another_root_level_timer');
        Magento_Profiler::stop('another_root_level_timer');
        Magento_Profiler::disable();
        Magento_Profiler::start('this_timer_should_be_ignored');
        Magento_Profiler::stop('this_timer_should_be_ignored');
        $expectedTimers = array(
            'some_root_timer',
            'some_root_timer->some_nested_timer',
            'some_root_timer->some_nested_timer->some_deeply_nested_timer',
            'one_more_root_timer',
            'another_root_level_timer',
        );
        $actualTimers = Magento_Profiler::getTimers();
        $this->assertEquals($expectedTimers, $actualTimers);
    }

    public function testEnable()
    {
        Magento_Profiler::start('this_timer_should_be_ignored');
        Magento_Profiler::stop('this_timer_should_be_ignored');
        Magento_Profiler::enable();
        Magento_Profiler::start('another_root_level_timer');
        Magento_Profiler::start('another_nested_timer');
        Magento_Profiler::stop('another_nested_timer');
        Magento_Profiler::stop('another_root_level_timer');
        $expectedTimers = array(
            'another_root_level_timer',
            'another_root_level_timer->another_nested_timer',
        );
        $actualTimers = Magento_Profiler::getTimers();
        $this->assertEquals($expectedTimers, $actualTimers);
    }

    public function testResetProfiler()
    {
        Magento_Profiler::reset();
        $this->assertEquals(array(), Magento_Profiler::getTimers());
    }

    public function testResetTimer()
    {
        $this->markTestIncomplete();
    }

    public function testStart()
    {
        Magento_Profiler::reset();
        $expectedTimers = array(
            'another_root_level_timer',
            'another_root_level_timer->another_nested_timer'
        );
        Magento_Profiler::start('another_root_level_timer');
        Magento_Profiler::start('another_nested_timer');
        Magento_Profiler::stop('another_nested_timer');
        Magento_Profiler::stop('another_root_level_timer');
        $actualTimers = Magento_Profiler::getTimers();
        $this->assertEquals($expectedTimers, $actualTimers);
    }

    /**
     * @expectedException Varien_Exception
     */
    public function testStartException()
    {
        Magento_Profiler::start('another_root_level_timer->another_nested_timer');
    }

    public function stopDataProvider()
    {
        return array(
            'omit timer name' => array(
                array(array(), array())
            ),
            'null timer name' => array(
                array(array(null), array(null))
            ),
            'pass timer name' => array(
                array(array('another_nested_timer'), array('another_root_level_timer'))
            ),
        );
    }

    /**
     * @dataProvider stopDataProvider
     */
    public function testStop(array $stopArgumentSets)
    {
        $stopCallback = array('Magento_Profiler', 'stop');

        Magento_Profiler::start('another_root_level_timer');
        Magento_Profiler::start('another_nested_timer');
        foreach ($stopArgumentSets as $stopArguments) {
            call_user_func_array($stopCallback, $stopArguments);
        }
        Magento_Profiler::start('one_more_root_timer');
        Magento_Profiler::stop('one_more_root_timer');

        $expected = array(
            'some_root_timer',
            'some_root_timer->some_nested_timer',
            'some_root_timer->some_nested_timer->some_deeply_nested_timer',
            'one_more_root_timer',
            'another_root_level_timer',
            'another_root_level_timer->another_nested_timer',
        );

        $actual = Magento_Profiler::getTimers();
        $this->assertEquals($expected, $actual);
    }

    public function stopExceptionDataProvider()
    {
        return array(
            'stop non-started timer' => array(
                array('another_root_level_timer'), array('non_started_timer')
            ),
            'stop order violation' => array(
                array('another_root_level_timer', 'another_nested_timer'), array('another_root_level_timer')
            ),
        );
    }

    /**
     * @dataProvider stopExceptionDataProvider
     * @expectedException Varien_Exception
     */
    public function testStopException(array $timersToStart, array $timersToStop)
    {
        foreach ($timersToStart as $timerName) {
            Magento_Profiler::start($timerName);
        }
        foreach ($timersToStop as $timerName) {
            Magento_Profiler::stop($timerName);
        }
    }
}
