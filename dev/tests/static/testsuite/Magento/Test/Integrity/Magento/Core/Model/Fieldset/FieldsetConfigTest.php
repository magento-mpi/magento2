<?php
/**
 * Find "fieldset.xml" files and validate them
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Magento\Core\Model\Fieldset;

class FieldsetConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $configFile
     *
     * @dataProvider xmlDataProvider
     */
    public function testXml($configFile)
    {
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents($configFile));
        $schema = \Magento\TestFramework\Utility\Files::init()->getPathToSource()
            . '/app/code/Magento/Core/etc/fieldset_file.xsd';
        $errors = \Magento\Config\Dom::validateDomDocument($dom, $schema);
        if ($errors) {
            $this->fail('XML-file ' . $configFile . ' has validation errors:'
                . PHP_EOL . implode(PHP_EOL . PHP_EOL, $errors));
        }
    }

    public function testSchemaUsingValidXml()
    {
        $xmlFile = __DIR__ . '/_files/fieldset.xml';
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $schema = \Magento\TestFramework\Utility\Files::init()->getPathToSource()
            . '/app/code/Magento/Core/etc/fieldset.xsd';
        $errors = \Magento\Config\Dom::validateDomDocument($dom, $schema);
        if ($errors) {
            $this->fail('There is a problem with the schema.  A known good XML file failed validation: '
                . PHP_EOL . implode(PHP_EOL . PHP_EOL, $errors));
        }
    }

    public function testSchemaUsingInvalidXml()
    {
        $xmlFile = __DIR__ . '/_files/invalid_fieldset.xml';
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $schema = \Magento\TestFramework\Utility\Files::init()->getPathToSource()
            . '/app/code/Magento/Core/etc/fieldset.xsd';
        $errors = \Magento\Config\Dom::validateDomDocument($dom, $schema);
        if (!$errors) {
            $this->fail('There is a problem with the schema.  A known bad XML file passed validation');
        }
    }

    public function testFileSchemaUsingValidXml()
    {
        $xmlFile = __DIR__ . '/_files/fieldset_file.xml';
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $schema = \Magento\TestFramework\Utility\Files::init()->getPathToSource()
            . '/app/code/Magento/Core/etc/fieldset_file.xsd';
        $errors = \Magento\Config\Dom::validateDomDocument($dom, $schema);
        if ($errors) {
            $this->fail('There is a problem with the schema.  A known good XML file failed validation: '
                . PHP_EOL . implode(PHP_EOL . PHP_EOL, $errors));
        }
    }

    public function testFileSchemaUsingInvalidXml()
    {
        $xmlFile = __DIR__ . '/_files/invalid_fieldset.xml';
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $schema = \Magento\TestFramework\Utility\Files::init()->getPathToSource()
            . '/app/code/Magento/Core/etc/fieldset_file.xsd';
        $errors = \Magento\Config\Dom::validateDomDocument($dom, $schema);
        if (!$errors) {
            $this->fail('There is a problem with the schema.  A known bad XML file passed validation');
        }
    }

    /**
     * @return array
     */
    public function xmlDataProvider()
    {
        return \Magento\TestFramework\Utility\Files::init()->getConfigFiles('fieldset.xml', array(), true);
    }
}
