<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_PrintedTemplate_Model_Source_AllowedLocalesTest extends PHPUnit_Framework_TestCase
{
   /**
    * test toOptionArray method
    *
    * @param array $locales
    * @params array $expected
    *
    * @dataProvider testToOptionArrayProvider
    */
    public function testToOptionArray($locales, $expected)
    {
        $configMock = $this->getMockBuilder('Saas_PrintedTemplate_Model_Config')
            ->setMethods(array('getConfigSectionArray'))
            ->getMock();

        $configMock->expects($this->any())
            ->method('getConfigSectionArray')
            ->with($this->equalTo('allowed_locales'))
            ->will($this->returnValue($locales));

        $model = $this->getMockBuilder('Saas_PrintedTemplate_Model_Source_AllowedLocales')
            ->setMethods(array('_getConfigModelSingeleton'))
            ->getMock();

        $model->expects($this->once())
            ->method('_getConfigModelSingeleton')
            ->will($this->returnValue($configMock));

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
                array('en_US' => 'English (United States) / English (United States)',
                    'en_GB' => 'English (United Kingdom) / English (United Kingdom)'
                ),
                array(
                    array('value' => 'en_US', 'label' => 'English (United States) / English (United States)'),
                    array('value' => 'en_GB', 'label' => 'English (United Kingdom) / English (United Kingdom)'))
            ),
            array(array(), array()),
        );
    }
}
