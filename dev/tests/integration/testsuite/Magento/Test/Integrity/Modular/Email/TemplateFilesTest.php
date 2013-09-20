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
     * @param string $module
     * @param string $filename
     * @dataProvider loadBaseContentsDataProvider
     */
    public function testLoadBaseContents($module, $filename)
    {
        $model = Mage::getModel('Magento_Core_Model_Email_Template');
        $this->assertNotEmpty($model->loadBaseContents($module, $filename));
    }

    public function loadBaseContentsDataProvider()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $templateModel = $objectManager->create('Magento_Core_Model_Email_Template');
        $configModel = $objectManager->get('Magento_Core_Model_Config');
        
        $data = array();
        foreach ($templateModel->getDefaultTemplates() as $row) {
            $data[] = array($configModel->determineOmittedNamespace($row['@']['module'], true), $row['file']);
        }
        return $data;
    }
}
