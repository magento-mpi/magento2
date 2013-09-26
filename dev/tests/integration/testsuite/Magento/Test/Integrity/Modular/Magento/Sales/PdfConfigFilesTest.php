<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Modular_Magento_Sales_PdfConfigFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @dataProvider fileFormatDataProvider
     */
    public function testFileFormat($file)
    {
        /** @var Magento_Sales_Model_Order_Pdf_Config_SchemaLocator $schemaLocator */
        $schemaLocator = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Sales_Model_Order_Pdf_Config_SchemaLocator');
        $schemaFile = $schemaLocator->getPerFileSchema();

        $dom = new Magento_Config_Dom(file_get_contents($file));
        $result = $dom->validate($schemaFile, $errors);
        $this->assertTrue($result, print_r($errors, true));
    }

    /**
     * @return array
     */
    public function fileFormatDataProvider()
    {
        return Magento_TestFramework_Utility_Files::init()->getConfigFiles('pdf.xml');
    }

    public function testMergedFormat()
    {
        $validationState = $this->getMock('Magento_Config_ValidationStateInterface');
        $validationState->expects($this->any())
            ->method('isValidated')
            ->will($this->returnValue(true));

        /** @var Magento_Sales_Model_Order_Pdf_Config_Reader $reader */
        $reader = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order_Pdf_Config_Reader', array('validationState' => $validationState));
        try {
            $reader->read();
        } catch (Exception $e) {
            $this->fail('Merged pdf.xml files do not pass XSD validation: ' . $e->getMessage());
        }
    }
}
