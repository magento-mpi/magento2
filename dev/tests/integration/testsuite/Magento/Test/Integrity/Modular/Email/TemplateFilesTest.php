<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_Integrity_Modular_Email_TemplateFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * Go through all declared templates and check if base files indeed exist in the respective module
     *
     * @param string $templateId
     * @dataProvider loadBaseContentsDataProvider
     */
    public function testLoadBaseContents($templateId)
    {
        /** @var Magento_Core_Model_Email_Template_Config $emailConfig */
        $emailConfig = Mage::getModel('Magento_Core_Model_Email_Template_Config');
        $templateFilename = $emailConfig->getTemplateFilename($templateId);
        $this->assertFileExists($templateFilename, 'Email template file, specified in the configuration, must exist');
    }

    public function loadBaseContentsDataProvider()
    {
        $data = array();
        /** @var Magento_Core_Model_Email_Template_Config $emailConfig */
        $emailConfig = Mage::getModel('Magento_Core_Model_Email_Template_Config');
        foreach ($emailConfig->getAvailableTemplates() as $templateId) {
            $data[$templateId] = array($templateId);
        }
        return $data;
    }
}
