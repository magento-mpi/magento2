<?php
/**
 * An abstract test class for XML/XSD validation
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Integrity_ConfigAbstract extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $configFile
     *
     * @dataProvider schemaDataProvider
     */
    public function testXml($configFile)
    {
        $schema = Magento_TestFramework_Utility_Files::init()->getPathToSource() . $this->_getXsd();
        $this->_validateFileExpectSuccess($configFile, $schema);
    }

    /**
     * Returns the name of the XSD file to be used to validate the XML
     *
     * @return string
     */
    abstract protected function _getXsd();

    public function testSchemaUsingValidXml()
    {
        $xmlFile = $this->_getKnownValidXml();
        $schema = Magento_TestFramework_Utility_Files::init()->getPathToSource() . $this->_getXsd();
        $this->_validateFileExpectSuccess($xmlFile, $schema);
    }

    /**
     * The location of a single valid complete xml file
     *
     * @return string
     */
    abstract protected function _getKnownValidXml();

    public function testSchemaUsingInvalidXml()
    {
        $xmlFile = $this->_getKnownInvalidXml();
        $schema = Magento_TestFramework_Utility_Files::init()->getPathToSource() . $this->_getXsd();
        $this->_validateFileExpectFailure($xmlFile, $schema);
    }

    /**
     * The location of a single known invalid complete xml file
     *
     * @return string
     */
    abstract protected function _getKnownInvalidXml();

    public function testFileSchemaUsingPartialXml()
    {
        $xmlFile = $this->_getKnownValidPartialXml();
        $schema = Magento_TestFramework_Utility_Files::init()->getPathToSource() . $this->_getFileXsd();
        $this->_validateFileExpectSuccess($xmlFile, $schema);
    }

    /**
     * The location of a single known valid partial xml file
     *
     * @return string
     */
    abstract protected function _getKnownValidPartialXml();

    /**
     * Returns the name of the XSD file to be used to validate partial XML
     *
     * @return string
     */
    abstract protected function _getFileXsd();

    public function testFileSchemaUsingInvalidXml()
    {
        $xmlFile = $this->_getKnownInvalidPartialXml();
        $schema = Magento_TestFramework_Utility_Files::init()->getPathToSource() . $this->_getFileXsd();
        $this->_validateFileExpectFailure($xmlFile, $schema);
    }

    /**
     * The location of a single known invalid partial xml file
     *
     * @return string
     */
    abstract protected function _getKnownInvalidPartialXml();


    public function testSchemaUsingPartialXml()
    {
        $xmlFile = $this->_getKnownValidPartialXml();;
        $schema = Magento_TestFramework_Utility_Files::init()->getPathToSource() . $this->_getXsd();
        $this->_validateFileExpectFailure($xmlFile, $schema);
    }

    /**
     * @return array
     */
    public function schemaDataProvider()
    {
        return Magento_TestFramework_Utility_Files::init()->getConfigFiles($this->_getXmlName());
    }

    /**
     * Returns the name of the xml files to validate
     *
     * @return string
     */
    abstract protected function _getXmlName();

    /**
     * Run schema validation against a known bad xml file with a provided schema.
     *
     * This helper expects the validation to fail and will fail a test if no errors are found.
     *
     * @param $xmlFile string a known bad xml file.
     * @param $schemaFile string schema that should find errors in the known bad xml file.
     */
    protected function _validateFileExpectSuccess($xmlFile, $schemaFile)
    {
        $dom = new DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $errors = Magento_Config_Dom::validateDomDocument($dom, $schemaFile);
        if ($errors) {
            $this->fail('There is a problem with the schema.  A known good XML file failed validation: '
                        . PHP_EOL . implode(PHP_EOL . PHP_EOL, $errors));
        }
    }

    /**
     * Run schema validation against an xml file with a provided schema.
     *
     * This helper expects the validation to pass and will fail a test if any errors are found.
     *
     * @param $xmlFile string a known good xml file.
     * @param $schemaFile string schema that should find no errors in the known good xml file.
     */
    protected function _validateFileExpectFailure($xmlFile, $schemaFile)
    {
        $dom = new DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $errors = Magento_Config_Dom::validateDomDocument($dom, $schemaFile);
        if (!$errors) {
            $this->fail('There is a problem with the schema.  A known bad XML file passed validation');
        }
    }
}