<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Config\Initial;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Config\Initial\Converter
     */
    protected $_model;

    protected function setUp()
    {
        $nodeMap = array(
            'default' => '/config/default',
            'stores' => '/config/stores',
            'websites' => '/config/websites',
        );
        $this->_model = new \Magento\App\Config\Initial\Converter($nodeMap);
    }

    public function testConvert()
    {
        $fixturePath = __DIR__ . '/_files/';
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents($fixturePath . 'config.xml'));
        $expectedResult = include $fixturePath . 'converted_config.php';
        $this->assertEquals($expectedResult, $this->_model->convert($dom));
    }
}
