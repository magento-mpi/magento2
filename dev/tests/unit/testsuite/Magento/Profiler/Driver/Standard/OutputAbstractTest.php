<?php
/**
 * Test class for Magento_Profiler_Driver_Standard_OutputAbstract
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_Driver_Standard_OutputAbstractStatTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Profiler_Driver_Standard_OutputAbstract
     */
    protected $_output;

    protected function setUp()
    {
        $this->_output = $this->getMockForAbstractClass('Magento_Profiler_Driver_Standard_OutputAbstract');
    }

    /**
     * Test setFilterPattern method
     */
    public function testSetFilterPattern()
    {
        $this->assertAttributeEmpty('_filterPattern', $this->_output);
        $filterPattern = '/test/';
        $this->_output->setFilterPattern($filterPattern);
        $this->assertAttributeEquals($filterPattern, '_filterPattern', $this->_output);
    }

    /**
     * Test setThreshold method
     */
    public function testSetThreshold()
    {
        $thresholdKey = Magento_Profiler_Driver_Standard_Stat::TIME;
        $this->_output->setThreshold($thresholdKey, 100);
        $thresholds = PHPUnit_Util_Class::getObjectAttribute($this->_output, '_thresholds');
        $this->assertArrayHasKey($thresholdKey, $thresholds);
        $this->assertEquals(100, $thresholds[$thresholdKey]);

        $this->_output->setThreshold($thresholdKey, null);
        $thresholds = PHPUnit_Util_Class::getObjectAttribute($this->_output, '_thresholds');
        $this->assertArrayNotHasKey($thresholdKey, $thresholds);
    }
}
