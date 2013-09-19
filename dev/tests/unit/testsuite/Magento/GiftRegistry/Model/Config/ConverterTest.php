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
    public function testConvertWithoutTranslate()
    {
        $this->_source->loadXML(file_get_contents($this->_filePath . 'giftregistry_config_without_translation.xml'));
        $convertedFile = include ($this->_filePath . 'giftregistry_config.php');
        $converted = $this->_model->convert($this->_source);

        $this->assertEquals($converted, $convertedFile);

    }

    /**
     * Test if there are translated nodes inside xml
     */
    public function testConvertWithTranslate()
    {
        $this->_source->loadXML(file_get_contents($this->_filePath . 'giftregistry_config_with_translation.xml'));
        $converted = $this->_model->convert($this->_source);

        $this->assertInstanceOf('Magento_Phrase', $converted['attribute_types']['text']['label']);
        $this->assertInstanceOf('Magento_Phrase', $converted['attribute_groups']['event_information']['label']);
        $this->assertInstanceOf(
            'Magento_Phrase',
            $converted['registry']['static_attributes']['event_country']['label']
        );
        $this->assertInstanceOf('Magento_Phrase', $converted['registrant']['static_attributes']['role']['label']);
        $this->assertInstanceOf(
            'Magento_Phrase',
            $converted['registry']['custom_attributes']['my_event_special']['label']
        );
        $this->assertInstanceOf(
            'Magento_Phrase',
            $converted['registrant']['custom_attributes']['my_special_attribute']['label']
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @param string $invalidConfigFileName
     * @dataProvider invalidConfigFilesDataProvider
     */
    public function testConvertThrowsExceptionWhenDomIsInvalid($invalidConfigFileName)
    {
        $this->_source->loadXML(file_get_contents($this->_filePath . $invalidConfigFileName));
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
            array('giftregistry_config_invalid1.xml'),
            array('giftregistry_config_invalid2.xml'),
            array('giftregistry_config_invalid3.xml'),
            array('giftregistry_config_invalid4.xml')
        );
    }
}
