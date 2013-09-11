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
        $data = array();
        /** @var $configModel Magento_Core_Model_Config */
        $configModel = Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Config');
        foreach (Magento_Core_Model_Email_Template::getDefaultTemplates() as $row) {
            $data[] = array($configModel->determineOmittedNamespace($row['@']['module'], true), $row['file']);
        }
        return $data;
    }
}
