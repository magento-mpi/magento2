<?php
/**
 * \Magento\Framework\Object\Copy\Config\Converter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Object\Copy\Config;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Object\Copy\Config\Converter
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new \Magento\Framework\Object\Copy\Config\Converter();
    }

    public function testConvert()
    {
        $dom = new \DOMDocument();
        $xmlFile = __DIR__ . '/_files/fieldset.xml';
        $dom->loadXML(file_get_contents($xmlFile));

        $convertedFile = __DIR__ . '/_files/fieldset_config.php';
        $expectedResult = include $convertedFile;
        $this->assertEquals($expectedResult, $this->_model->convert($dom));
    }
}
