<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Resource\Config;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Resource\Config\Converter
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_filePath;

    /**
     * @var \DOMDocument
     */
    protected $_source;

    protected function setUp()
    {
        $this->_filePath = __DIR__ . '/_files/';
        $this->_source = new \DOMDocument();
        $this->_model = new \Magento\App\Resource\Config\Converter();
    }

    public function testConvert()
    {
        $this->_source->loadXML(file_get_contents($this->_filePath . 'resources.xml'));
        $convertedFile = include ($this->_filePath . 'resources.php');
        $this->assertEquals($convertedFile, $this->_model->convert($this->_source));
    }
}
