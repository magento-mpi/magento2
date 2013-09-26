<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Resource_Config_XsdTest extends PHPUnit_Framework_TestCase
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
        $this->markTestIncomplete();
        $this->_xsdSchema = BP . '/app/code/Magento/Core/etc/resources.xsd';
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
        $xmlString = file_get_contents(__DIR__ . '/_files/valid_resources.xml');
        $actualResult = $this->_xsdValidator->validate($this->_xsdSchema, $xmlString);

        $this->assertEmpty($actualResult);
    }

    /**
     * Data provider with invalid xml array according to resources.xsd
     */
    public function schemaCorrectlyIdentifiesInvalidXmlDataProvider()
    {
        return include(__DIR__ . '/_files/invalidResourcesXmlArray.php');
    }
}