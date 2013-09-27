<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\Model\Config;

class XsdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Path to xsd file
     * @var string
     */
    protected $_xsdSchemaPath;

    /**
     * @var \Magento\TestFramework\Utility\XsdValidator
     */
    protected $_xsdValidator;

    protected function setUp()
    {
        $this->_xsdSchemaPath = BP . '/app/code/Magento/Index/etc/';
        $this->_xsdValidator = new \Magento\TestFramework\Utility\XsdValidator();
    }

    /**
     * @param string $schemaName
     * @param string $xmlString
     * @param array $expectedError
     */
    protected function _loadDataForTest($schemaName, $xmlString, $expectedError)
    {
        $actualError = $this->_xsdValidator->validate($this->_xsdSchemaPath . $schemaName, $xmlString);
        $this->assertEquals($expectedError, $actualError);
    }

    /**
     * @param string $xmlString
     * @param array $expectedError
     * @dataProvider schemaCorrectlyIdentifiesInvalidIndexersXmlDataProvider
     */
    public function testSchemaCorrectlyIdentifiesInvalidIndexersXml($xmlString, $expectedError)
    {
        $this->_loadDataForTest('indexers.xsd', $xmlString, $expectedError);
    }

    /**
     * @param string $xmlString
     * @param array $expectedError
     * @dataProvider schemaCorrectlyIdentifiesInvalidIndexersMergedXmlDataProvider
     */
    public function testSchemaCorrectlyIdentifiesInvalidIndexersMergedXml($xmlString, $expectedError)
    {
        $this->_loadDataForTest('indexers_merged.xsd', $xmlString, $expectedError);
    }

    /**
     * @param string $schemaName
     * @param string $validFileName
     * @dataProvider schemaCorrectlyIdentifiesValidXmlDataProvider
     */
    public function testSchemaCorrectlyIdentifiesValidXml($schemaName, $validFileName)
    {
        $xmlString = file_get_contents(__DIR__ . '/_files/' . $validFileName);
        $schemaPath = $this->_xsdSchemaPath . $schemaName;
        $actualResult = $this->_xsdValidator->validate($schemaPath, $xmlString);
        $this->assertEquals(array(), $actualResult);
    }

    /**
     * Data provider with valid xml files according to schemas
     */
    public function schemaCorrectlyIdentifiesValidXmlDataProvider()
    {
        return array(
            'indexers' => array('indexers.xsd', 'valid_indexers.xml'),
            'indexers_merged' => array('indexers_merged.xsd', 'valid_indexers_merged.xml')
        );
    }

    /**
     * Data provider with invalid xml array according to schema
     */
    public function schemaCorrectlyIdentifiesInvalidIndexersXmlDataProvider()
    {
        return include(__DIR__ . '/_files/invalidIndexersXmlArray.php');
    }

    /**
     * Data provider with invalid xml array according to schema
     */
    public function schemaCorrectlyIdentifiesInvalidIndexersMergedXmlDataProvider()
    {
        return include(__DIR__ . '/_files/invalidIndexersXmlMergedArray.php');
    }
}
