<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_PrintedTemplate_Model_Source_MeasurementTest extends PHPUnit_Framework_TestCase
{

    public function translate($text)
    {
        return $text;
    }

    /**
     * test toOptionArray method
     *
     * @param array $measurement
     * @params array $expected
     *
     * @dataProvider testToOptionArrayProvider
     */
    public function testToOptionArray($measurement, $expected)
    {
        $configMock = $this->getMockBuilder('Saas_PrintedTemplate_Model_Config')
            ->setMethods(array('getConfigSectionArray'))
            ->getMock();

        $configMock->expects($this->any())
            ->method('getConfigSectionArray')
            ->with($this->equalTo('measurements'))
            ->will($this->returnValue($measurement));

        $model = $this->getMockBuilder('Saas_PrintedTemplate_Model_Source_Measurement')
            ->setMethods(array('_getConfigModel', '_getHelper'))
            ->getMock();

        $model->expects($this->once())
            ->method('_getConfigModel')
            ->will($this->returnValue($configMock));

        $helperMock = $this->getMockBuilder('Saas_PrintedTemplate_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('__'))
            ->getMock();

        $helperMock->expects($this->any())
            ->method('__')
            ->will($this->returnCallback(array($this, 'translate')));

        $model->expects($this->any())
            ->method('_getHelper')
            ->will($this->returnValue($helperMock));

        $optionArray = $model->toOptionArray();
        $this->assertSame($expected, $optionArray);
    }

    /**
     * provide data for toOptionArrayProvider method
     *
     * @return array
     */
    public function testToOptionArrayProvider()
    {
        return array(
            array(
                array(
                    'Millimeters' => array('label' => 'Millimeters', 'value' => 0),
                    'Inches' => array('label' => 'Inches', 'value' => 1)
                ),
                array('MILLIMETERS' => 'Millimeters', 'INCHES' => 'Inches')
            ),
            array(array(), array()),
        );
    }
}
