<?php
/**
 * An abstract test class for XML/XSD validation
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\Integrity;

abstract class AbstractConfig extends \PHPUnit_Framework_TestCase
{
    public function testXmlFiles()
    {
        $invoker = new \Magento\TestFramework\Utility\AggregateInvoker($this);
        $invoker(
            /**
             * @param string $configFile
             */
            function ($configFile) {
                $schema = \Magento\TestFramework\Utility\Files::init()->getPathToSource() . $this->_getXsd();
                $fileSchema = \Magento\TestFramework\Utility\Files::init()->getPathToSource() . $this->_getFileXsd();
                $this->_validateFileExpectSuccess($configFile, $schema, $fileSchema);
            },
            \Magento\TestFramework\Utility\Files::init()->getConfigFiles($this->_getXmlName())
        );
    }

    public function testSchemaUsingValidXml()
    {
        $xmlFile = $this->_getKnownValidXml();
        $schema = \Magento\TestFramework\Utility\Files::init()->getPathToSource() . $this->_getXsd();
        $this->_validateFileExpectSuccess($xmlFile, $schema);
    }

    public function testSchemaUsingInvalidXml($expectedErrors = null)
    {
        $xmlFile = $this->_getKnownInvalidXml();
        $schema = \Magento\TestFramework\Utility\Files::init()->getPathToSource() . $this->_getXsd();
        $this->_validateFileExpectFailure($xmlFile, $schema, $expectedErrors);
    }

    public function testFileSchemaUsingPartialXml()
    {
        $xmlFile = $this->_getKnownValidPartialXml();
        $schema = \Magento\TestFramework\Utility\Files::init()->getPathToSource() . $this->_getFileXsd();
        $this->_validateFileExpectSuccess($xmlFile, $schema);
    }

    public function testFileSchemaUsingInvalidXml($expectedErrors = null)
    {
        $xmlFile = $this->_getKnownInvalidPartialXml();
        $schema = \Magento\TestFramework\Utility\Files::init()->getPathToSource() . $this->_getFileXsd();
        $this->_validateFileExpectFailure($xmlFile, $schema, $expectedErrors);
    }

    public function testSchemaUsingPartialXml($expectedErrors = null)
    {
        $xmlFile = $this->_getKnownValidPartialXml();
        $schema = \Magento\TestFramework\Utility\Files::init()->getPathToSource() . $this->_getXsd();
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
    protected function _validateFileExpectSuccess($xmlFile, $schemaFile, $fileSchemaFile = null)
    {
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $errors = \Magento\Config\Dom::validateDomDocument($dom, $schemaFile);
        if ($errors) {
            if (!is_null($fileSchemaFile)) {
                $moreErrors = \Magento\Config\Dom::validateDomDocument($dom, $fileSchemaFile);
                if (empty($moreErrors)) {
                    return;
                } else {
                    $errors = array_merge($errors, $moreErrors);
                }
            }
            $this->fail(
                'There is a problem with the schema.  A known good XML file failed validation: ' . PHP_EOL . implode(
                    PHP_EOL . PHP_EOL,
                    $errors
                )
            );
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
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $actualErrors = \Magento\Config\Dom::validateDomDocument($dom, $schemaFile);

        if (isset($expectedErrors)) {
            $this->assertNotEmpty(
                $actualErrors,
                'No schema validation errors found, expected errors: ' . PHP_EOL . implode(PHP_EOL, $expectedErrors)
            );
            foreach ($expectedErrors as $expectedError) {
                $found = false;

                foreach ($actualErrors as $errorKey => $actualError) {
                    if (!(strpos($actualError, $expectedError) === false)) {
                        // found expected string
                        $found = true;
                        // remove found error from list of actual errors
                        unset($actualErrors[$errorKey]);
                        break;
                    }
                }
                $this->assertTrue(
                    $found,
                    'Failed asserting that ' . $expectedError . " is in: \n" . implode(PHP_EOL, $actualErrors)
                );
            }
            // list of actual errors should now be empty
            $this->assertEmpty($actualErrors, "There were unexpected errors: \n" . implode(PHP_EOL, $actualErrors));
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
