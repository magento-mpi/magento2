<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Data\Argument;

class XsdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Path to xsd schema file for validating argument types
     * @var string
     */
    protected $_typesXsdSchema;

    /**
     * @var \Magento\TestFramework\Utility\XsdValidator
     */
    protected $_xsdValidator;

    protected function setUp()
    {
        $this->_typesXsdSchema = __DIR__ . "/_files/types_schema.xsd";
        $this->_xsdValidator = new \Magento\TestFramework\Utility\XsdValidator();
    }

    /**
     * @param string $xmlString
     * @param array $expectedError
     * @dataProvider schemaCorrectlyIdentifiesInvalidTypesXmlDataProvider
     */
    public function testSchemaCorrectlyIdentifiesInvalidTypesXml($xmlString, $expectedError)
    {
        $actualError = $this->_xsdValidator->validate($this->_typesXsdSchema, $xmlString);
        $this->assertEquals($expectedError, $actualError);
    }

    /**
     * Data provider with invalid type declaration
     *
     * @return array
     */
    public function schemaCorrectlyIdentifiesInvalidTypesXmlDataProvider()
    {
        return include(__DIR__ . '/_files/typesInvalidArray.php');
    }

    public function testSchemaCorrectlyIdentifiesValidXml()
    {
        $xmlString = file_get_contents(__DIR__ . '/_files/types_valid.xml');
        $actualResult = $this->_xsdValidator->validate($this->_typesXsdSchema, $xmlString);

        $this->assertEmpty($actualResult, join("\n", $actualResult));
    }
}
