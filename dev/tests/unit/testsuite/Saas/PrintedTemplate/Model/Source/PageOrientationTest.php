<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_PrintedTemplate_Model_Source_PageOrientationTest extends PHPUnit_Framework_TestCase
{

    public function translate($text)
    {
        return $text;
    }

   /**
    * test toOptionArray method
    *
    * @param array $orientations
    * @params array $expected
    *
    * @dataProvider testToOptionArrayProvider
    */
    public function testToOptionArray($orientations, $expected)
    {
        $configMock = $this->getMockBuilder('Saas_PrintedTemplate_Model_Config')
            ->setMethods(array('getConfigSectionArray'))
            ->getMock();

        $configMock->expects($this->any())
            ->method('getConfigSectionArray')
            ->with($this->equalTo('page_orientation'))
            ->will($this->returnValue($orientations));

        $model = $this->getMockBuilder('Saas_PrintedTemplate_Model_Source_PageOrientation')
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
                    'album' => array('label' => 'Album', 'value' => 12),
                    'portrait' => array('label' => 'Portrait', 'value' => 11)
                ),
                array('album' => 'Album', 'portrait' => 'Portrait')
            ),
            array(array(), array()),
        );
    }
}
