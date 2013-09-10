<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_FullPageCache_Model_Placeholder_Config_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_FullPageCache_Model_Placeholder_Config_Converter
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Magento_FullPageCache_Model_Placeholder_Config_Converter();
    }

    public function testConvert()
    {
        $dom = new DOMDocument();
        $file = realpath(__DIR__ . '/_files/placeholders.xml');
        $dom->load($file);
        $expected = array (
            'blockInstanceOne' => array(
                array(
                    'code' => 'codeOne',
                    'cache_lifetime' => 86400,
                    'container' => 'containerInstanceOne',
                ),
                array(
                    'code' => 'codeTwo',
                    'cache_lifetime' => 0,
                    'container' => 'containerInstanceTwo',
                    'name' => 'blockNameThree',
                ),
            ),
            'blockInstanceTwo' => array (
                array(
                    'code' => 'codeTwo',
                    'cache_lifetime' => 86400,
                    'container' => 'containerInstanceTwo',
                    'name' => 'blockNameTwo',
                ),
            ),
        );
        $actual = $this->_model->convert($dom);
        $this->assertEquals($expected, $actual);
    }
}