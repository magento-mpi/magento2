<?php
/**
 * Test class for Magento_Profiler_Driver_Standard_Output_Configuration
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_Driver_Standard_Output_ConfigurationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Profiler_Driver_Standard_Output_Configuration
     */
    protected $_configuration;

    protected function setUp()
    {
        $this->_configuration = new Magento_Profiler_Driver_Standard_Output_Configuration();
    }

    public function testHasFilteredPatternValue()
    {
        $this->assertFalse($this->_configuration->hasFilterPatternValue());
        $this->_configuration->setFilterPatternValue('test');
        $this->assertTrue($this->_configuration->hasFilterPatternValue());
    }

    public function testGetAndSetFilteredPatternValue()
    {
        $this->assertEquals('default', $this->_configuration->getFilterPatternValue('default'));
        $this->_configuration->setFilterPatternValue('test');
        $this->assertEquals('test', $this->_configuration->getFilterPatternValue());
    }

    public function testHasThresholdsValue()
    {
        $this->assertFalse($this->_configuration->hasThresholdsValue());
        $this->_configuration->setThresholdsValue(array(
            'fetchKey' => 100
        ));
        $this->assertTrue($this->_configuration->hasThresholdsValue());
    }

    public function testGetAndSetThresholdsValue()
    {
        $this->assertEquals(
            array('fetchKey' => 100),
            $this->_configuration->getThresholdsValue(array('fetchKey' => 100))
        );
        $this->_configuration->setThresholdsValue(array('fetchKey' => 100));
        $this->assertEquals(array('fetchKey' => 100), $this->_configuration->getThresholdsValue());
    }
}
