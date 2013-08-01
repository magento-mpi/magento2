<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_PrintedTemplate_Model_Source_FontTest extends PHPUnit_Framework_TestCase
{
    /**
     * Emulate text translation
     *
     * @param string $text
     * @return string
     */
    public function translate($text)
    {
        return ($text . '_translated');
    }

    /**
     * test toOptionArray method
     *
     * @param array $fonts
     * @params array $expected
     *
     * @dataProvider testToOptionArrayProvider
     */
    public function testToOptionArray($fonts, $expected)
    {
        $configModel = new Magento_Object();
        $configModel->setFontsArray($fonts);

        $model = $this->getMockBuilder('Saas_PrintedTemplate_Model_Source_Font')
            ->setMethods(array('_getConfigModelSingeleton', '_getHelper'))
            ->getMock();

        $model->expects($this->once())
            ->method('_getConfigModelSingeleton')
            ->will($this->returnValue($configModel));

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
                    'arial' => array('css' => 'arial', 'label' => 'Arial'),
                    'serif' => array('css' => 'serif', 'label' => 'Serif')
                ),
                array('arial' => $this->translate('Arial'), 'serif' => $this->translate('Serif'))
            ),
            array(
                array(array('CustomFont')),
                array()
            ),
            array(
                array(array('')),
                array()
            )
        );
    }
}
