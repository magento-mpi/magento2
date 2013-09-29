<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\Model\Indexer\Config;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Index\Model\Indexer\Config\Converter
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\Index\Model\Indexer\Config\Converter();
    }

    /**
     * @covers \Magento\Index\Model\Indexer\Config\Converter::convert
     */
    public function testConvert()
    {
        $basePath = realpath(__DIR__) . '/_files/';
        $path = $basePath . 'indexers.xml';
        $domDocument = new \DOMDocument();
        $domDocument->load($path);
        $expectedData = include($basePath . 'indexers.php');
        $this->assertEquals($expectedData, $this->_model->convert($domDocument));
    }
}
