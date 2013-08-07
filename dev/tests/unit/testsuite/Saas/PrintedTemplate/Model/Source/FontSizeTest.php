<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_PrintedTemplate_Model_Source_FontSizeTest extends PHPUnit_Framework_TestCase
{
    /**
     * test toOptionArray method
     *
     * @param array $sizes
     * @params array $expected
     *
     * @dataProvider testToOptionArrayProvider
     */
    public function testToOptionArray($sizes, $expected)
    {
        $configModel = new Magento_Object();
        $configModel->setFontSizesArray($sizes);

        $model = $this->getMockBuilder('Saas_PrintedTemplate_Model_Source_FontSize')
            ->disableOriginalConstructor()
            ->setMethods(array('_getConfigModelSingeleton'))
            ->getMock();

        $model->expects($this->once())
            ->method('_getConfigModelSingeleton')
            ->will($this->returnValue($configModel));

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
                        array('8', '12', '16', '24'),
                        array(
                                8 => '8',
                                12 => '12',
                                16 => '16',
                                24 => '24'
                        )
                ),
                array(array('1'), array(1 => '1')),
                array(array(''), array('' => ''))
        );
    }
}
