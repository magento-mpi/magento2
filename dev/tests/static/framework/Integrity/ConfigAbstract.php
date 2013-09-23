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
    /** indicator no config file found */
    const NO_CONFIG_FILE = 'no config file';

    /**
     * @param string $configFile
     *
     * @dataProvider configFilesDataProvider
     */
    public function testXml($configFile)
    {
        if ($configFile == self::NO_CONFIG_FILE) {
            $this->markTestSkipped(
                'There is no config file to test.'
            );
        }
        $schema = Magento_TestFramework_Utility_Files::init()->getPathToSource() . $this->_getXsd();
        $fileSchema = Magento_TestFramework_Utility_Files::init()->getPathToSource() . $this->_getFileXsd();
        $this->_validateFileExpectSuccess($configFile, $schema, $fileSchema);
    }

    /**
     * @return array
     */
    public function configFilesDataProvider()
    {
        $fileList = Magento_TestFramework_Utility_Files::init()->getConfigFiles($this->_getXmlName());
        return empty($fileList) ? array(self::NO_CONFIG_FILE => array(self::NO_CONFIG_FILE)) : $fileList;
    }

    public function testSchemaUsingValidXml()
    {
        $xmlFile = $this->_getKnownValidXml();
        $schema = Magento_TestFramework_Utility_Files::init()->getPathToSource() . $this->_getXsd();
        $this->_validateFileExpectSuccess($xmlFile, $schema);
    }

    public function testSchemaUsingInvalidXml($expectedErrors = null)
    {
        $xmlFile = $this->_getKnownInvalidXml();
        $schema = Magento_TestFramework_Utility_Files::init()->getPathToSource() . $this->_getXsd();
        $this->_validateFileExpectFailure($xmlFile, $schema, $expectedErrors);
    }

    public function testFileSchemaUsingPartialXml()
    {
        $xmlFile = $this->_getKnownValidPartialXml();
        $schema = Magento_TestFramework_Utility_Files::init()->getPathToSource() . $this->_getFileXsd();
        $this->_validateFileExpectSuccess($xmlFile, $schema);
    }

    public function testFileSchemaUsingInvalidXml($expectedErrors = null)
    {
        $xmlFile = $this->_getKnownInvalidPartialXml();
        $schema = Magento_TestFramework_Utility_Files::init()->getPathToSource() . $this->_getFileXsd();
        $this->_validateFileExpectFailure($xmlFile, $schema, $expectedErrors);
    }

    public function testSchemaUsingPartialXml($expectedErrors = null)
    {
        $xmlFile = $this->_getKnownValidPartialXml();;
        $schema = Magento_TestFramework_Utility_Files::init()->getPathToSource() . $this->_getXsd();
        $this->_validateFileExpectFailure($xmlFile, $schema, $expectedErrors);
    }

    /**
     * Run schema validation against a known bad xml file with a provided schema.
     *
     * This helper expects the validation to fail and will fail a test if no errors are found.
     *
     * @param $xmlFile string a known bad xml file.
     * @param $schemaFile string schema that should find errors in the known bad xml file.
     * @param $fileSchemaFile string schema that should find errors in the known bad xml file
     */
    protected function _validateFileExpectSuccess($xmlFile, $schemaFile, $fileSchemaFile=null)
    {
        $dom = new DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $errors = Magento_Config_Dom::validateDomDocument($dom, $schemaFile);
        if ($errors) {
            if (!is_null($fileSchemaFile)) {
                $moreErrors = Magento_Config_Dom::validateDomDocument($dom, $fileSchemaFile);
                if (empty($moreErrors)) {
                    return;
                } else {
                    $errors = array_merge($errors, $moreErrors);
                }
            }
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
     * @param $expectedErrors null|array that may contain a list of expected errors.  Each element can be a substring
     *   of an error, but all errors must be listed.
     */
    protected function _validateFileExpectFailure($xmlFile, $schemaFile, $expectedErrors = null)
    {
        $dom = new DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $actualErrors = Magento_Config_Dom::validateDomDocument($dom, $schemaFile);

        if (isset($expectedErrors)) {
            $this->assertNotEmpty(
                $actualErrors,
                'No schema validation errors found, expected errors '. implode('; ', $expectedErrors)
            );
            foreach ($expectedErrors as $expectedError) {
                $found = false;

                foreach ($actualErrors as $errorKey => $actualError) {
                    if (!(strpos($actualError, $expectedError) === false)) {
                        // found expected string
                        $found = true;
                        break;
                    }
                }
                $this->assertTrue(
                    $found,
                    'Failed asserting that '. $expectedError . ' is in: ' . implode(', ', $actualErrors)
                );
                // remove found error from list of actual errors
                unset($actualErrors[$errorKey]);
            }
            // list of actual errors should now be empty
            $this->assertEmpty($actualErrors, 'There were unexpected errors: ' . implode('; ', $actualErrors));
        } elseif (!$actualErrors) {
            $this->fail('There is a problem with the schema.  A known bad XML file passed validation');
        }
    }

    /**
     * Returns the name of the XSD file to be used to validate the XML
     *
     * @return string
     */
    abstract protected function _getXsd();

    /**
     * The location of a single valid complete xml file
     *
     * @return string
     */
    abstract protected function _getKnownValidXml();

    /**
     * The location of a single known invalid complete xml file
     *
     * @return string
     */
    abstract protected function _getKnownInvalidXml();

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

    /**
     * The location of a single known invalid partial xml file
     *
     * @return string
     */
    abstract protected function _getKnownInvalidPartialXml();

    /**
     * Returns the name of the xml files to validate
     *
     * @return string
     */
    abstract protected function _getXmlName();
}