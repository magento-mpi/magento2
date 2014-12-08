<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Model\Config;

class XsdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * File path for xsd
     *
     * @var string
     */
    protected $_xsdFilePath;

    public function setUp()
    {
        $this->_xsdFilePath = __DIR__ . '/../../../../../../../../app/code/Magento/GiftRegistry/etc/giftregistry.xsd';
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
        $dom = new \DOMDocument();
        $dom->load(__DIR__ . '/../_files/' . $xmlFile);
        libxml_use_internal_errors(true);
        $dom->schemaValidate($this->_xsdFilePath);

        $errors = libxml_get_errors();
        $errorMessages = [];

        foreach ($errors as $error) {
            $errorMessages[] = $error->message;
        }
        libxml_use_internal_errors(false);
        $this->assertEquals($errorMessages, $expectedErrors);
    }

    /**
     * Tests valid xml file
     */
    public function testValidXmlFile()
    {
        $dom = new \DOMDocument();
        $dom->load(__DIR__ . '/../_files/config_valid.xml');
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
        return [
            [
                'config_invalid_attribute_group.xml',
                [
                    "Element 'attribute_group': Duplicate key-sequence ['registry'] " .
                    "in unique identity-constraint 'uniqueAttributeGroupName'.\n"
                ],
            ],
            [
                'config_invalid_attribute_type.xml',
                [
                    "Element 'attribute_type': Duplicate key-sequence ['text'] " .
                    "in unique identity-constraint 'uniqueAttributeTypeName'.\n"
                ]
            ],
            [
                'config_invalid_static_attribute.xml',
                [
                    "Element 'static_attribute': Duplicate key-sequence ['event_date'] " .
                    "in unique identity-constraint 'uniqueStaticAttributeName'.\n"
                ]
            ],
            [
                'config_invalid_custom_attribute.xml',
                [
                    "Element 'custom_attribute': Duplicate key-sequence ['custom_event_data'] " .
                    "in unique identity-constraint 'uniqueCustomAttributeName'.\n"
                ]
            ]
        ];
    }
}
