<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ImportExport\Model\Import\Config;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ImportExport\Model\Import\Config\Converter
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_filePath;

    public function setUp()
    {
        $this->_filePath = realpath(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
        $this->_model = new \Magento\ImportExport\Model\Import\Config\Converter();
    }

    public function testConvert()
    {
        $testDom = $this->_filePath . 'import.xml';
        $dom = new \DOMDocument();
        $dom->load($testDom);
        $expectedArray = include($this->_filePath . 'import.php');
        $this->assertEquals($expectedArray, $this->_model->convert($dom));
    }
}
