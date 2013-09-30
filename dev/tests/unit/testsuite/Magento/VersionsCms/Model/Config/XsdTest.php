<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_VersionsCms_Model_Config_XsdTest extends PHPUnit_Framework_TestCase
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
        $this->_xsdSchema = BP . '/app/code/Magento/VersionsCms/etc/menu_hierarchy.xsd';
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
        $xmlString = file_get_contents(__DIR__ . '/_files/valid_menu_hierarchy.xml');
        $actualResult = $this->_xsdValidator->validate($this->_xsdSchema, $xmlString);
        $this->assertEmpty($actualResult);
    }

    /**
     * Data provider with invalid xml array according to menu_hierarchy.xsd
     */
    public function schemaCorrectlyIdentifiesInvalidXmlDataProvider()
    {
        return include(__DIR__ . '/_files/invalidMenuHierarchyXmlArray.php');
    }
}