<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Eav_Model_Entity_Attribute_Config_XsdTest extends PHPUnit_Framework_TestCase
{
    /**
     * Path to xsd schema file
     * @var string
     */
    protected $_xsdSchema;

    /**
     * @var Magento_TestFramework_Utility_XsdValidator
     */
    protected $_xsdValidator;

    protected function setUp()
    {
        $this->_xsdSchema = BP . '/app/code/Magento/Eav/etc/eav_attributes.xsd';
        $this->_xsdValidator = new Magento_TestFramework_Utility_XsdValidator();
    }

    /**
     * @param string $xmlString
     * @param array $expectedError
     * @dataProvider schemaCorrectlyIdentifiesInvalidXmlDataProvider
     */
    public function testSchemaCorrectlyIdentifiesInvalidXml($xmlString, $expectedError)
    {
        $actualError = $this->_xsdValidator->validate($this->_xsdSchema, $xmlString);
        $this->assertEquals($expectedError, $actualError);
    }

    public function testSchemaCorrectlyIdentifiesValidXml()
    {
        $xmlString = file_get_contents(__DIR__ . '/_files/eav_attributes.xml');
        $actualResult = $this->_xsdValidator->validate($this->_xsdSchema, $xmlString);
        $this->assertEmpty($actualResult);
    }

    /**
     * Data provider with invalid xml array according to eav_attribute.xsd
     */
    public function schemaCorrectlyIdentifiesInvalidXmlDataProvider()
    {
        return include(__DIR__ . '/_files/invalidEavAttributeXmlArray.php');
    }
}