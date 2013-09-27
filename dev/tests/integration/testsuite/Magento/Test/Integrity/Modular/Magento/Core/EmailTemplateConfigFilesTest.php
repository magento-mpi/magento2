<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Modular_Magento_Core_EmailTemplateConfigFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test that email template configuration file matches the format
     *
     * @param string $file
     * @dataProvider fileFormatDataProvider
     */
    public function testFileFormat($file)
    {
        $schemaFile = BP . '/app/code/Magento/Core/etc/email_templates_file.xsd';
        $dom = new Magento_Config_Dom(file_get_contents($file));
        $result = $dom->validate($schemaFile, $errors);
        $this->assertTrue($result, print_r($errors, true));
    }

    /**
     * @return array
     */
    public function fileFormatDataProvider()
    {
        return Magento_TestFramework_Utility_Files::init()->getConfigFiles('email_templates.xml');
    }

    /**
     * Test that email template configuration contains references to existing template files
     *
     * @param string $templateId
     * @dataProvider templateReferenceDataProvider
     */
    public function testTemplateReference($templateId)
    {
        /** @var Magento_Core_Model_Email_Template_Config $emailConfig */
        $emailConfig =  Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Email_Template_Config');
        $templateFilename = $emailConfig->getTemplateFilename($templateId);
        $this->assertFileExists($templateFilename, 'Email template file, specified in the configuration, must exist');
    }

    /**
     * @return array
     */
    public function templateReferenceDataProvider()
    {
        $data = array();
        /** @var Magento_Core_Model_Email_Template_Config $emailConfig */
        $emailConfig =  Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Email_Template_Config');
        foreach ($emailConfig->getAvailableTemplates() as $templateId) {
            $data[$templateId] = array($templateId);
        }
        return $data;
    }

    /**
     * Test that merged configuration of email templates matches the format
     */
    public function testMergedFormat()
    {
        $validationState = $this->getMock('Magento_Config_ValidationStateInterface');
        $validationState->expects($this->any())->method('isValidated')->will($this->returnValue(true));
        /** @var Magento_Core_Model_Email_Template_Config_Reader $reader */
        $reader = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Email_Template_Config_Reader', array('validationState' => $validationState));
        try {
            $reader->read();
        } catch (Exception $e) {
            $this->fail('Merged email templates configuration does not pass XSD validation: ' . $e->getMessage());
        }
    }
}
