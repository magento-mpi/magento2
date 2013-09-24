<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Model_Config_XsdTest extends PHPUnit_Framework_TestCase
{
    /**
     * File path for xsd
     *
     * @var string
     */
    protected $_xsdFilePath;

    public function setUp()
    {
        $this->_xsdFilePath =  __DIR__ . '/../../../../../../../../app/code/Magento/GiftRegistry/etc/giftregistry.xsd';
    }

    /**
     * Tests different cases with invalid xml files
     *
     * @dataProvider invalidXmlFileDataProvider
     * @param string $xmlFile
     * @param array $expectedErrors
     */
    public function testInvalidXmlFile($xmlFile, $expectedErrors)
    {
        $dom = new DOMDocument();
        $dom->load(__DIR__. '/../_files/' . $xmlFile);
        libxml_use_internal_errors(true);
        $dom->schemaValidate($this->_xsdFilePath);

        $errors = libxml_get_errors();
        $errorMessages = array();

        foreach ($errors as $error) {
            $errorMessages[] = $error->message;
        }
        libxml_use_internal_errors(false);
        $this->assertEquals($errorMessages, $expectedErrors);
    }

    /**
     * Tests valid xml file
     *
     * @param string $xmlFile
     * @dataProvider validXmlFileDataProvider
     */
    public function testValidXmlFile($xmlFile)
    {
        $dom = new DOMDocument();
        $dom->load(__DIR__. '/../_files/' . $xmlFile);
        libxml_use_internal_errors(true);
        $result = $dom->schemaValidate($this->_xsdFilePath);
        libxml_use_internal_errors(false);
        $this->assertTrue($result);
    }

    /**
     * @return array
     */
    public function invalidXmlFileDataProvider()
    {
        return array(
            array(
               'config_invalid_attribute_group.xml',
                array(
                    "Element 'attribute_group': Duplicate key-sequence ['registry'] " .
                    "in unique identity-constraint 'uniqueAttributeGroupName'.\n"
                )
            ),
            array(
                'config_invalid_attribute_type.xml',
                array(
                    "Element 'attribute_type': Duplicate key-sequence ['text'] " .
                    "in unique identity-constraint 'uniqueAttributeTypeName'.\n"
                )
            ),
            array(
                'config_invalid_static_attribute.xml',
                array(
                    "Element 'static_attribute': Duplicate key-sequence ['event_date'] " .
                     "in unique identity-constraint 'uniqueStaticAttributeName'.\n"
                )
            ),
            array(
                'config_invalid_custom_attribute.xml',
                array(
                    "Element 'custom_attribute': Duplicate key-sequence ['custom_event_data'] " .
                     "in unique identity-constraint 'uniqueCustomAttributeName'.\n"
                )
            )
        );
    }

    /**
     * @return array
     */
    public function validXmlFileDataProvider()
    {
        return array(
            array('config_valid.xml')
        );
    }
}
