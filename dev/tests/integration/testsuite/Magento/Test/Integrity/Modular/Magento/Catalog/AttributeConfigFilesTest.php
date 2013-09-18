<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Modular_Magento_Catalog_AttributeConfigFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_schemaFile;

    protected function setUp()
    {
        /** @var Magento_Catalog_Model_Attribute_Config_SchemaLocator $schemaLocator */
        $schemaLocator = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Catalog_Model_Attribute_Config_SchemaLocator');
        $this->_schemaFile = $schemaLocator->getSchema();
    }

    /**
     * @param string $file
     * @dataProvider fileFormatDataProvider
     */
    public function testFileFormat($file)
    {
        $dom = new Magento_Config_Dom(file_get_contents($file));
        $result = $dom->validate($this->_schemaFile, $errors);
        $this->assertTrue($result, print_r($errors, true));
    }

    /**
     * @return array
     */
    public function fileFormatDataProvider()
    {
        return Magento_TestFramework_Utility_Files::init()->getConfigFiles('catalog_attributes.xml');
    }
}
