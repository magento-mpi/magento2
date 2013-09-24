<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config\Initial;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Config\Initial\Converter
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\Core\Model\Config\Initial\Converter();
    }

    public function testConvert()
    {
        $fixturePath = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents($fixturePath . 'config.xml'));
        $expectedResult = include $fixturePath . 'converted_config.php';
        $this->assertEquals($expectedResult, $this->_model->convert($dom));
    }
}
