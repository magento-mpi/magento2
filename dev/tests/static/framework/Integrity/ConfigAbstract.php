<?php
/**
 * An abstract test class for XML/XSD validation
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Integrity_ConfigAbstract extends PHPUnit_Framework_TestCase {
    /**
     * @param string $configFile
     *
     * @dataProvider schemaDataProvider
     */
    public function testSchema($configFile)
    {
        $dom = new DOMDocument();
        $dom->loadXML(file_get_contents($configFile));
        $schema = Utility_Files::init()->getPathToSource() . $this->_getXSDFile();
        $errors = Magento_Config_Dom::validateDomDocument($dom, $schema);
        if ($errors) {
            $this->fail('XML-file ' . $configFile . ' has validation errors:'
                        . PHP_EOL . implode(PHP_EOL . PHP_EOL, $errors));
        }
    }

    /**
     * @return array
     */
    public function schemaDataProvider()
    {
        return Utility_Files::init()->getConfigFiles($this->_getXMLName());
    }

    /**
     * Returns the name of the xml files to validate
     *
     * @return string
     */
    abstract protected function _getXMLName();

    /**
     * Returns the name of the XSD file to be used to validate the XSD
     *
     * @return string
     */
    abstract protected function _getXSDFile();
}