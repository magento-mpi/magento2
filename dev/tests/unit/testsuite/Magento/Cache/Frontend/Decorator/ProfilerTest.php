<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Cache_Frontend_Decorator_ProfilerTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Magento_Profiler::enable();
    }

    public function tearDown()
    {
        Magento_Profiler::reset();
    }

    /**
     * @param string $method
     * @param array $params
     * @param Zend_Cache_Backend $cacheBackend
     * @param Zend_Cache_Core $cacheFrontend
     * @param string $expectedProfileId
     * @param array $expectedProfilerTags
     * @param mixed $expectedResult
     * @dataProvider proxyMethodDataProvider
     */
    public function testProxyMethod($method, $params, $cacheBackend, $cacheFrontend, $expectedProfileId,
        $expectedProfilerTags, $expectedResult
    ) {
        // Cache frontend setup
        $frontendMock = $this->getMock('Magento_Cache_FrontendInterface');

        $frontendMock->expects($this->any())
            ->method('getBackend')
            ->will($this->returnValue($cacheBackend));

        $frontendMock->expects($this->any())
            ->method('getLowLevelFrontend')
            ->will($this->returnValue($cacheFrontend));

        // Profiler setup
        $driver = $this->getMock('Magento_Profiler_DriverInterface');
        $driver->expects($this->once())
            ->method('start')
            ->with($expectedProfileId, $expectedProfilerTags);
        $driver->expects($this->once())
            ->method('stop')
            ->with($expectedProfileId);
        Magento_Profiler::add($driver);

        // Test
        $object = new Magento_Cache_Frontend_Decorator_Profiler($frontendMock, array('Zend_Cache_Backend_'));
        $helper = new Magento_TestFramework_Helper_ProxyTesting();
        $result = $helper->invokeWithExpectations($object, $frontendMock, $method, $params, $expectedResult);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @return array
     */
    public static function proxyMethodDataProvider()
    {
        $backend = new Zend_Cache_Backend_BlackHole;;
        $adaptee = PHPUnit_Framework_MockObject_Generator::getMock('Zend_Cache_Core', array(), array(), '', false);
        $lowLevelFrontend = new Magento_Cache_Frontend_Adapter_Zend($adaptee);

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
                    'frontend_type' => 'Magento_Cache_Frontend_Adapter_Zend',
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
                    'frontend_type' => 'Magento_Cache_Frontend_Adapter_Zend',
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
                    'frontend_type' => 'Magento_Cache_Frontend_Adapter_Zend',
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
                    'frontend_type' => 'Magento_Cache_Frontend_Adapter_Zend',
                    'backend_type'  => 'BlackHole',
                ),
                true
            ),
            array(
                'clean',
                array(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array('tag')),
                $backend,
                $lowLevelFrontend,
                'cache_clean',
                array(
                    'group'         => 'cache',
                    'operation'     => 'cache:clean',
                    'frontend_type' => 'Magento_Cache_Frontend_Adapter_Zend',
                    'backend_type'  => 'BlackHole',
                ),
                true
            ),
        );
    }
}
