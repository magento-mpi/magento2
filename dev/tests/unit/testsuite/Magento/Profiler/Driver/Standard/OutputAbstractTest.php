<?php
/**
 * Test class for Magento_Profiler_Driver_Standard_OutputAbstract
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_Driver_Standard_OutputAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Profiler_Driver_Standard_OutputAbstract|PHPUnit_Framework_MockObject_MockObject
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
        $this->assertEquals($filterPattern, $this->_output->getFilterPattern());
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
        $this->assertArrayNotHasKey($thresholdKey, $this->_output->getThresholds());
    }

    /**
     * Test __construct method
     */
    public function testConstructor()
    {
        $configuration = array(
            'filterPattern' => '/filter pattern/',
            'thresholds' => array(
                'fetchKey' => 100
            )
        );
        /** @var Magento_Profiler_Driver_Standard_OutputAbstract|PHPUnit_Framework_MockObject_MockObject $output  */
        $output = $this->getMockForAbstractClass('Magento_Profiler_Driver_Standard_OutputAbstract', array(
            $configuration
        ));
        $this->assertEquals('/filter pattern/', $output->getFilterPattern());
        $thresholds = $output->getThresholds();
        $this->assertArrayHasKey('fetchKey', $thresholds);
        $this->assertEquals(100, $thresholds['fetchKey']);
    }

    /**
     * Test _renderColumnValue method
     *
     * @dataProvider renderColumnValueDataProvider
     * @param mixed $value
     * @param string $columnKey
     * @param mixed $expectedValue
     */
    public function testRenderColumnValue($value, $columnKey, $expectedValue)
    {
        $method = new ReflectionMethod($this->_output, '_renderColumnValue');
        $method->setAccessible(true);
        $this->assertEquals($expectedValue, $method->invoke($this->_output, $value, $columnKey));
    }

    /**
     * @return array
     */
    public function renderColumnValueDataProvider()
    {
        return array(
            array(
                'someTimerId',
                Magento_Profiler_Driver_Standard_Stat::ID,
                'someTimerId'
            ),
            array(
                10000.123,
                Magento_Profiler_Driver_Standard_Stat::TIME,
                '10,000.123000'
            ),
            array(
                200000.123456789,
                Magento_Profiler_Driver_Standard_Stat::AVG,
                '200,000.123457'
            ),
            array(
                1000000000.12345678,
                Magento_Profiler_Driver_Standard_Stat::EMALLOC,
                '1,000,000,000'
            ),
            array(
                2000000000.12345678,
                Magento_Profiler_Driver_Standard_Stat::REALMEM,
                '2,000,000,000'
            ),
        );
    }

    /**
     * Test _renderCaption method
     */
    public function testRenderCaption()
    {
        $method = new ReflectionMethod($this->_output, '_renderCaption');
        $method->setAccessible(true);
        $this->assertRegExp(
            '/Code Profiler \(Memory usage: real - \d+, emalloc - \d+\)/',
            $method->invoke($this->_output)
        );
    }

    /**
     * Test _getTimerIds method
     */
    public function testGetTimerIds()
    {
        $this->_output->setFilterPattern('/filter pattern/');

        $mockStat = $this->getMock('Magento_Profiler_Driver_Standard_Stat');
        $expectedTimerIds = array('test');
        $mockStat->expects($this->once())
            ->method('getFilteredTimerIds')
            ->with($this->_output->getThresholds(), $this->_output->getFilterPattern())
            ->will($this->returnValue($expectedTimerIds));

        $method = new ReflectionMethod($this->_output, '_getTimerIds');
        $method->setAccessible(true);
        $this->assertEquals($expectedTimerIds, $method->invoke($this->_output, $mockStat));
    }

    /**
     * Test _renderTimerId method
     */
    public function testRenderTimerId()
    {
        $method = new ReflectionMethod($this->_output, '_renderTimerId');
        $method->setAccessible(true);
        $this->assertEquals('someTimerId', $method->invoke($this->_output, 'someTimerId'));
    }
}
