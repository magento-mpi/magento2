<?php
/**
 * Find "widget.xml" files and validate them
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Integrity_Mage_Widget_WidgetConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $configFile
     *
     * @dataProvider schemaDataProvider
     */
    public function testSchema($configFile)
    {
        $dom = new DOMDocument();
        $dom->loadXML(file_get_contents($configFile));
        $schema = Utility_Files::init()->getPathToSource() . '/app/code/Mage/Widget/etc/widget.xsd';
        $errors = Magento_Config_Dom::validateDomDocument($dom, $schema);
        if ($errors) {
            $this->fail('XML-file has validation errors:' . PHP_EOL . implode(PHP_EOL . PHP_EOL, $errors));
        }
    }

    /**
     * @return array
     */
    public function schemaDataProvider()
    {
        // Include design directory (last parameter)
        return Utility_Files::init()->getConfigFiles('widget.xml', array(), true, true);
    }
}
