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
        $dom = new DOMDocument();
        $dom->loadXML(file_get_contents($configFile));
        $schema = Utility_Files::init()->getPathToSource() . $this->_getXsd();
        $errors = Magento_Config_Dom::validateDomDocument($dom, $schema);
        if ($errors) {
            $this->fail(
                'XML-file ' . $configFile . ' has validation errors:'
                . PHP_EOL . implode(PHP_EOL . PHP_EOL, $errors)
            );
        }
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
        $dom = new DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $schema = Utility_Files::init()->getPathToSource() . $this->_getXsd();
        $errors = Magento_Config_Dom::validateDomDocument($dom, $schema);
        if ($errors) {
            $this->fail(
                'There is a problem with the schema.  A known good XML file failed validation: '
                . PHP_EOL . implode(PHP_EOL . PHP_EOL, $errors)
            );
        }
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
        $dom = new DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $schema = Utility_Files::init()->getPathToSource() . $this->_getXsd();
        $errors = Magento_Config_Dom::validateDomDocument($dom, $schema);
        if (!$errors) {
            $this->fail('There is a problem with the schema.  A known bad XML file passed validation');
        }
    }

    /**
     * The location of a single known invalid complete xml file
     *
     * @return string
     */
    abstract protected function _getKnownInvalidXml();

    public function testFileSchemaUsingXml()
    {
        $xmlFile = $this->_getKnownValidPartialXml();
        $dom = new DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $schema = Utility_Files::init()->getPathToSource() . $this->_getFileXsd();
        $errors = Magento_Config_Dom::validateDomDocument($dom, $schema);
        if ($errors) {
            $this->fail(
                'There is a problem with the schema.  A known good XML file failed validation: '
                . PHP_EOL . implode(PHP_EOL . PHP_EOL, $errors)
            );
        }
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
        $dom = new DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $schema = Utility_Files::init()->getPathToSource() . $this->_getFileXsd();
        $errors = Magento_Config_Dom::validateDomDocument($dom, $schema);
        if (!$errors) {
            $this->fail('There is a problem with the schema.  A known bad XML file passed validation');
        }
    }

    /**
     * The location of a single known invalid partial xml file
     *
     * @return string
     */
    abstract protected function _getKnownInvalidPartialXml();

    /**
     * @return array
     */
    public function schemaDataProvider()
    {
        return Utility_Files::init()->getConfigFiles($this->_getXmlName());
    }

    /**
     * Returns the name of the xml files to validate
     *
     * @return string
     */
    abstract protected function _getXmlName();
}