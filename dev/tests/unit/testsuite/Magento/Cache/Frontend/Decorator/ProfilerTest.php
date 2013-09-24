<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cache\Frontend\Decorator;

class ProfilerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        \Magento\Profiler::enable();
    }

    protected function tearDown()
    {
        \Magento\Profiler::reset();
    }

    /**
     * @param string $method
     * @param array $params
     * @param \Zend_Cache_Backend $cacheBackend
     * @param \Zend_Cache_Core $cacheFrontend
     * @param string $expectedProfileId
     * @param array $expectedProfilerTags
     * @param mixed $expectedResult
     * @dataProvider proxyMethodDataProvider
     */
    public function testProxyMethod($method, $params, $cacheBackend, $cacheFrontend, $expectedProfileId,
        $expectedProfilerTags, $expectedResult
    ) {
        // Cache frontend setup
        $frontendMock = $this->getMock('Magento\Cache\FrontendInterface');

        $frontendMock->expects($this->any())
            ->method('getBackend')
            ->will($this->returnValue($cacheBackend));

        $frontendMock->expects($this->any())
            ->method('getLowLevelFrontend')
            ->will($this->returnValue($cacheFrontend));

        // Profiler setup
        $driver = $this->getMock('Magento\Profiler\DriverInterface');
        $driver->expects($this->once())
            ->method('start')
            ->with($expectedProfileId, $expectedProfilerTags);
        $driver->expects($this->once())
            ->method('stop')
            ->with($expectedProfileId);
        \Magento\Profiler::add($driver);

        // Test
        $object = new \Magento\Cache\Frontend\Decorator\Profiler($frontendMock, array('Zend_Cache_Backend_'));
        $helper = new \Magento\TestFramework\Helper\ProxyTesting();
        $result = $helper->invokeWithExpectations($object, $frontendMock, $method, $params, $expectedResult);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @return array
     */
    public static function proxyMethodDataProvider()
    {
        $backend = new \Zend_Cache_Backend_BlackHole;;
        $adaptee = \PHPUnit_Framework_MockObject_Generator::getMock('Zend_Cache_Core', array(), array(), '', false);
        $lowLevelFrontend = new \Magento\Cache\Frontend\Adapter\Zend($adaptee);

        return array(
            array(
                'test',
                array('record_id'),
                $backend,
                $lowLevelFrontend,
                'cache_test',
                array(
                    'group'         => 'cache',
                    'operation'     => 'cache:test',
                    'frontend_type' => 'Magento\Cache\Frontend\Adapter\Zend',
                    'backend_type'  => 'BlackHole',
                ),
                111
            ),
            array(
                'load',
                array('record_id'),
                $backend,
                $lowLevelFrontend,
                'cache_load',
                array(
                    'group'         => 'cache',
                    'operation'     => 'cache:load',
                    'frontend_type' => 'Magento\Cache\Frontend\Adapter\Zend',
                    'backend_type'  => 'BlackHole',
                ),
                '111'
            ),
            array(
                'save',
                array('record_value', 'record_id', array('tag'), 555),
                $backend,
                $lowLevelFrontend,
                'cache_save',
                array(
                    'group'         => 'cache',
                    'operation'     => 'cache:save',
                    'frontend_type' => 'Magento\Cache\Frontend\Adapter\Zend',
                    'backend_type'  => 'BlackHole',
                ),
                true
            ),
            array(
                'remove',
                array('record_id'),
                $backend,
                $lowLevelFrontend,
                'cache_remove',
                array(
                    'group'         => 'cache',
                    'operation'     => 'cache:remove',
                    'frontend_type' => 'Magento\Cache\Frontend\Adapter\Zend',
                    'backend_type'  => 'BlackHole',
                ),
                true
            ),
            array(
                'clean',
                array(\Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array('tag')),
                $backend,
                $lowLevelFrontend,
                'cache_clean',
                array(
                    'group'         => 'cache',
                    'operation'     => 'cache:clean',
                    'frontend_type' => 'Magento\Cache\Frontend\Adapter\Zend',
                    'backend_type'  => 'BlackHole',
                ),
                true
            ),
        );
    }
}
