<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_Integrity_Modular_Magento_Customer_AddressFormatsFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_schemaFile;

    protected function setUp()
    {
        /** @var Magento_Customer_Model_Address_Config_SchemaLocator $schemaLocator */
        $schemaLocator = Mage::getObjectManager()->get('Magento_Customer_Model_Address_Config_SchemaLocator');
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
        return Magento_TestFramework_Utility_Files::init()->getConfigFiles('address_formats.xml');
    }
}
