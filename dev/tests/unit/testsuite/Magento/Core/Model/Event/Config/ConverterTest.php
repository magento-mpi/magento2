<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Event_Config_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Event\Config\Converter
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_filePath;

    /**
     * @var DOMDocument
     */
    protected $_source;

    protected function setUp()
    {
        $this->_filePath = __DIR__ . DIRECTORY_SEPARATOR . '/../../_files' . DIRECTORY_SEPARATOR;
        $this->_source = new DOMDocument();
        $this->_model = new \Magento\Core\Model\Event\Config\Converter();
    }

    public function testConvert()
    {
        $this->_source->loadXML(file_get_contents($this->_filePath . 'event_config.xml'));
        $convertedFile = include ($this->_filePath . 'event_config.php');
        $this->assertEquals($convertedFile, $this->_model->convert($this->_source));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Attribute name is missed
     */
    public function testConvertThrowsExceptionWhenDomIsInvalid()
    {
        $this->_source->loadXML(file_get_contents($this->_filePath . 'event_invalid_config.xml'));
        $this->_model->convert($this->_source);
    }
}