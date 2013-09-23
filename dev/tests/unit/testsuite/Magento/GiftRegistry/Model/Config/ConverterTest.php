<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Check Config Reader and Converter to receive the right array of data
 *
 * Class Magento_GiftRegistry_Model_Config_ConverterTest
 */
class Magento_GiftRegistry_Model_Config_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_GiftRegistry_Model_Config_Converter
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

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->_filePath = __DIR__ . DIRECTORY_SEPARATOR . '../_files' . DIRECTORY_SEPARATOR;
        $this->_source = new DOMDocument();
        $this->_model = new Magento_GiftRegistry_Model_Config_Converter();
    }

    /**
     * Test Xml structure without translations
     */
    public function testConvert()
    {
        $this->_source->loadXML(file_get_contents($this->_filePath . 'config_valid.xml'));
        $convertedFile = include ($this->_filePath . 'giftregistry_config.php');
        $converted = $this->_model->convert($this->_source);

        $this->assertEquals($converted, $convertedFile);

    }

    /**
     * @expectedException InvalidArgumentException
     * @param string $invalidConfFileName
     * @dataProvider invalidConfigFilesDataProvider
     */
    public function testConvertThrowsExceptionWhenDomIsInvalid($invalidConfFileName)
    {
        $this->_source->loadXML(file_get_contents($this->_filePath . $invalidConfFileName));
        $this->_model->convert($this->_source);
    }

    /**
     * Data provider for testConvertThrowsExceptionWhenDomIsInvalid
     *
     * @return array
     */
    public function invalidConfigFilesDataProvider()
    {
        return array(
            array('config_absent_attribute_name_attrname.xml'),
            array('config_absent_attribute_group_attrname.xml'),
            array('config_absent_static_attribute_attrname.xml'),
            array('config_absent_custom_attribute_attrname.xml')
        );
    }
}
