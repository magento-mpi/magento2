<?php
/**
 * Find "fieldset.xml" files and validate them
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Integrity_Magento_Core_Model_Fieldset_FieldsetConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $configFile
     *
     * @dataProvider xmlDataProvider
     */
    public function testXml($configFile)
    {
        $dom = new DOMDocument();
        $dom->loadXML(file_get_contents($configFile));
        $schema = Utility_Files::init()->getPathToSource() . '/app/code/Magento/Core/etc/fieldset.xsd';
        $errors = Magento_Config_Dom::validateDomDocument($dom, $schema);
        if ($errors) {
            $this->fail('XML-file ' . $configFile . ' has validation errors:'
                . PHP_EOL . implode(PHP_EOL . PHP_EOL, $errors));
        }
    }

    public function testSchemaUsingValidXml()
    {
        $xmlFile = __DIR__ . '/_files/new_fieldset.xml';
        $dom = new DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $schema = Utility_Files::init()->getPathToSource() . '/app/code/Magento/Core/etc/fieldset.xsd';
        $errors = Magento_Config_Dom::validateDomDocument($dom, $schema);
        if ($errors) {
            $this->fail('There is a problem with the schema.  A known good XML file failed validation: '
                . PHP_EOL . implode(PHP_EOL . PHP_EOL, $errors));
        }
    }

    public function testSchemaUsingInvalidXml()
    {
        $xmlFile = __DIR__ . '/_files/old_fieldset.xml';
        $dom = new DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $schema = Utility_Files::init()->getPathToSource() . '/app/code/Magento/Core/etc/fieldset.xsd';
        $errors = Magento_Config_Dom::validateDomDocument($dom, $schema);
        if (!$errors) {
            $this->fail('There is a problem with the schema.  A known bad XML file passed validation');
        }
    }

    /**
     * @return array
     */
    public function xmlDataProvider()
    {
        return Utility_Files::init()->getConfigFiles('fieldset.xml', array(), true);
    }
}