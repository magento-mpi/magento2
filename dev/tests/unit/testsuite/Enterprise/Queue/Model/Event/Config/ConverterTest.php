<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Queue_Model_Event_Config_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Queue_Model_Event_Config_Converter
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
        $this->_filePath = __DIR__ . DIRECTORY_SEPARATOR . '/../../../_files' . DIRECTORY_SEPARATOR;
        $this->_source = new DOMDocument();
        $this->_model = new Enterprise_Queue_Model_Event_Config_Converter();
    }

    public function testConvert()
    {
        $this->_source->loadXML(file_get_contents($this->_filePath . 'event_config.xml'));
        $convertedFile = include ($this->_filePath . 'event_config.php');
        $this->assertEquals($convertedFile, $this->_model->convert($this->_source));
    }
}