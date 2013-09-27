<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Resource\Config;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Resource\Config\Converter
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
        $this->_filePath = __DIR__ . '/_files' . DIRECTORY_SEPARATOR;
        $this->_source = new \DOMDocument();
        $this->_model = new \Magento\Core\Model\Resource\Config\Converter();
    }

    /**
     * @covers \Magento\Core\Model\Resource\Config\Converter::convert
     */
    public function testConvert()
    {
        $this->_source->loadXML(file_get_contents($this->_filePath . 'resources.xml'));
        $convertedFile = include ($this->_filePath . 'resources.php');
        $this->assertEquals($convertedFile, $this->_model->convert($this->_source));
    }
}
