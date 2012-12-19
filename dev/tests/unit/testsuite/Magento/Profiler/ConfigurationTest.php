<?php
/**
 * Test class for Magento_Profiler_Configuration
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_ConfigurationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Profiler_Configuration
     */
    protected $_configuration;

    /**
     * @var string
     */
    protected $_baseDir = __DIR__;

    protected function setUp()
    {
        $this->_configuration = new Magento_Profiler_Configuration($this->_baseDir);
    }

    public function testGetBaseDir()
    {
        $this->assertEquals($this->_baseDir, $this->_configuration->getBaseDir());
    }

    /**
     * @dataProvider driverConfigurationsDataProvider
     * @param mixed $data
     * @param array $expected
     */
    public function testDriverConfigurations(array $data, array $expected)
    {
        $this->_configuration->initDriverConfigurations($data);
        $this->assertEquals($expected, $this->_configuration->getDriverConfigurations());
    }

    /**
     * @return array
     */
    public function driverConfigurationsDataProvider()
    {
        return array(
            'Empty configuration' => array(
                array(),
                array()
            ),
            'Several drivers' => array(
                array(
                    'foo' => array(
                        'fooOption' => 'fooOptionValue'
                    ),
                    'bar' => array(
                        'type' => 'barType',
                        'baseDir' => '/some/custom/dir',
                        'barOption' => 'barOptionValue'
                    ),
                ),
                array(
                    new Magento_Profiler_Driver_Configuration(array(
                        'type' => 'foo',
                        'fooOption' => 'fooOptionValue',
                        'baseDir' => $this->_baseDir,
                    )),
                    new Magento_Profiler_Driver_Configuration(array(
                        'type' => 'barType',
                        'barOption' => 'barOptionValue',
                        'baseDir' => '/some/custom/dir',
                    ))
                )
            ),
            'Drivers scalar values' => array(
                array(
                    'foo' => array(
                        'fooOption' => 'fooOptionValue'
                    ),
                    'bar' => 0,
                    'baz' => 1
                ),
                array(
                    new Magento_Profiler_Driver_Configuration(array(
                        'type' => 'foo',
                        'fooOption' => 'fooOptionValue',
                        'baseDir' => $this->_baseDir,
                    )),
                    new Magento_Profiler_Driver_Configuration(array(
                        'type' => 'baz',
                        'baseDir' => $this->_baseDir,
                    ))
                )
            )
        );
    }

    public function testTagFilters()
    {
        $tagFilters = array(
            'foo' => 'bar',
            'bar' => 'qux'
        );
        $this->_configuration->setTagFilters($tagFilters);
        $this->assertEquals($tagFilters, $this->_configuration->getTagFilters());
    }

    public function testDriverFactory()
    {
        $this->assertInstanceOf('Magento_Profiler_Driver_Factory', $this->_configuration->getDriverFactory());
        $driver = new Magento_Profiler_Driver_Factory();
        $this->_configuration->setDriverFactory($driver);
        $this->assertSame($driver, $this->_configuration->getDriverFactory());
    }

    public function testGetDriverFactory()
    {
        $this->assertInstanceOf('Magento_Profiler_Driver_Factory', $this->_configuration->getDriverFactory());
    }

    public function testSetDriverFactory()
    {
        $factory = $this->getMock('Magento_Profiler_Driver_Factory');
        $this->_configuration->setDriverFactory($factory);
        $this->assertSame($factory, $this->_configuration->getDriverFactory());
    }
}
