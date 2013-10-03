<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model\Placeholder\Config;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\FullPageCache\Model\Placeholder\Config\Converter
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\FullPageCache\Model\Placeholder\Config\Converter();
    }

    public function testConvert()
    {
        $dom = new \DOMDocument();
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
