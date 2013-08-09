<?php
/**
 * Find "backend/system.xml" files and validate them
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Magento_Backend_SystemConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $configFile
     * @dataProvider schemaDataProvider
     */
    public function testSchema($configFile)
    {
        $dom = new DOMDocument();
        $dom->loadXML(file_get_contents($configFile));
        $schema = Magento_TestFramework_Utility_Files::init()->getPathToSource() . '/app/code/Magento/Backend/etc/system_file.xsd';
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
        return Magento_TestFramework_Utility_Files::init()->getConfigFiles('adminhtml/system.xml', array());
    }
}
