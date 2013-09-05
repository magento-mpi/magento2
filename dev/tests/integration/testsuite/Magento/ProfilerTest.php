<?php
/**
 * Test case for \Magento\Profiler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_ProfilerTest extends PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        \Magento\Profiler::reset();
    }

    /**
     * @dataProvider applyConfigDataProvider
     * @param array $config
     * @param array $expectedDrivers
     */
    public function testApplyConfigWithDrivers(array $config, array $expectedDrivers)
    {
        \Magento\Profiler::applyConfig($config, '');
        $this->assertAttributeEquals($expectedDrivers, '_drivers', '\Magento\Profiler');
    }

    /**
     * @return array
     */
    public function applyConfigDataProvider()
    {
        return array(
            'Empty config does not create any driver' => array(
                'config' => array(),
                'drivers' => array()
            ),
            'Integer 0 does not create any driver' => array(
                'config' => array('drivers' => array(0)),
                'drivers' => array()
            ),
            'Integer 1 does creates standard driver' => array(
                'config' => array('drivers' => array(1)),
                'drivers' => array(new \Magento\Profiler\Driver\Standard())
            ),
            'Config array key sets driver type' => array(
                'configs' => array('drivers' => array('standard' => 1)),
                'drivers' => array(new \Magento\Profiler\Driver\Standard())
            ),
            'Config array key ignored when type set' => array(
                'config' => array(
                    'drivers' => array('custom' => array('type' => 'standard'))
                ),
                'drivers' => array(new \Magento\Profiler\Driver\Standard())
            ),
            'Config with outputs element as integer 1 creates output' => array(
                'config' => array(
                    'drivers' => array(array('outputs' => array('html' => 1))),
                    'baseDir' => '/some/base/dir'
                ),
                'drivers' => array(
                    new \Magento\Profiler\Driver\Standard(array(
                        'outputs' => array(array(
                            'type' => 'html',
                            'baseDir' => '/some/base/dir'
                        ))
                    ))
                )
            ),
            'Config with outputs element as integer 0 does not create output' => array(
                'config' => array(
                    'drivers' => array(array('outputs' => array('html' => 0)))
                ),
                'drivers' => array(new \Magento\Profiler\Driver\Standard())
            ),
            'Config with shortly defined outputs element' => array(
                'config' => array(
                    'drivers' => array(
                        array('outputs' => array('foo' => 'html'))
                    ),
                ),
                'drivers' => array(new \Magento\Profiler\Driver\Standard(array(
                        'outputs' => array(array('type' => 'html'))
                    ))
                )
            ),
            'Config with fully defined outputs element options' => array(
                'config' => array(
                    'drivers' => array(
                        array(
                            'outputs' => array(
                                'foo' => array(
                                    'type' => 'html',
                                    'filterName' => '/someFilter/',
                                    'thresholds' => array('someKey' => 123),
                                    'baseDir' => '/custom/dir'
                                )
                            )
                        )
                    )
                ),
                'drivers' => array(
                    new \Magento\Profiler\Driver\Standard(array(
                        'outputs' => array(array(
                            'type' => 'html',
                            'filterName' => '/someFilter/',
                            'thresholds' => array('someKey' => 123),
                            'baseDir' => '/custom/dir'
                        ))
                    )
                ))
            ),
            'Config with shortly defined output' => array(
                'config' => array(
                    'drivers' => array(
                        array('output' => 'html')
                    )
                ),
                'drivers' => array(
                    new \Magento\Profiler\Driver\Standard(array(
                        'outputs' => array(array(
                            'type' => 'html'
                        ))
                    ))
                )
            )
        );
    }
}
