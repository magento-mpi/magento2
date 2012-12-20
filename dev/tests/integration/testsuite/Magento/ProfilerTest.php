<?php
/**
 * Test case for Magento_Profiler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_ProfilerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_baseDir = '/some/base/dir';

    protected function tearDown()
    {
        Magento_Profiler::reset();
    }

    /**
     * @dataProvider applyConfigDataProvider
     * @param array $driversConfig
     * @param array $expectedDrivers
     */
    public function testApplyConfigWithDrivers(array $driversConfig, array $expectedDrivers)
    {
        $config = new Magento_Profiler_Configuration($this->_baseDir);
        $config->initDriverConfigurations($driversConfig);
        Magento_Profiler::applyConfig($config);
        $this->assertAttributeEquals($expectedDrivers, '_drivers', 'Magento_Profiler');
    }

    /**
     * @return array
     */
    public function applyConfigDataProvider()
    {
        return array(
            'Empty array creates standard driver' => array(
                'configs' => array(array()),
                'drivers' => array(new Magento_Profiler_Driver_Standard())
            ),
            'Integer 0 does not create any driver' => array(
                'configs' => array(0),
                'drivers' => array()
            ),
            'Config array key sets driver type' => array(
                'configs' => array('pinba' => 1),
                'drivers' => array(new Magento_Profiler_Driver_Pinba())
            ),
            'Config array key ignored when type set' => array(
                'configs' => array('pinba' => array('type' => 'standard')),
                'drivers' => array(new Magento_Profiler_Driver_Standard())
            ),
            'Config with outputs element as integer 1 creates output' => array(
                'configs' => array(array('outputs' => array('html' => 1))),
                'drivers' => array(
                    new Magento_Profiler_Driver_Standard(
                        new Magento_Profiler_Driver_Configuration(array(
                            'outputs' => array(array(
                                'type' => 'html',
                                'baseDir' => $this->_baseDir
                            ))
                        ))
                    )
                )
            ),
            'Config with outputs element as integer 0 does not create output' => array(
                'configs' => array(array('outputs' => array('html' => 0))),
                'drivers' => array(new Magento_Profiler_Driver_Standard())
            ),
            'Config with shortly defined outputs element' => array(
                'configs' => array(array('outputs' => array('foo' => 'html'))),
                'drivers' => array(
                    new Magento_Profiler_Driver_Standard(
                        new Magento_Profiler_Driver_Configuration(array(
                            'outputs' => array(array(
                                'type' => 'html',
                                'baseDir' => $this->_baseDir
                            ))
                        ))
                    )
                )
            ),
            'Config with fully defined outputs element options' => array(
                'configs' => array(
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
                ),
                'drivers' => array(
                    new Magento_Profiler_Driver_Standard(
                        new Magento_Profiler_Driver_Configuration(array(
                            'outputs' => array(array(
                                'type' => 'html',
                                'filterName' => '/someFilter/',
                                'thresholds' => array('someKey' => 123),
                                'baseDir' => '/custom/dir'
                            ))
                        ))
                    )
                )
            ),
            'Config with shortly defined output' => array(
                'configs' => array(array('output' => 'html')),
                'drivers' => array(
                    new Magento_Profiler_Driver_Standard(
                        new Magento_Profiler_Driver_Configuration(array(
                            'outputs' => array(array(
                                'type' => 'html',
                                'baseDir' => $this->_baseDir
                            ))
                        ))
                    )
                )
            ),
        );
    }
}
