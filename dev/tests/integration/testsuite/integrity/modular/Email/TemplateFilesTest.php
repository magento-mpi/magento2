<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group integrity
 */
class Integrity_Modular_Email_TemplateFilesTest extends PHPUnit_Framework_TestCase
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
        $model = new Mage_Core_Model_Email_Template;
        $this->assertNotEmpty($model->loadBaseContents($module, $filename));
    }

    public function loadBaseContentsDataProvider()
    {
        $data = array();
        $config = Mage::getConfig();
        foreach (Mage_Core_Model_Email_Template::getDefaultTemplates() as $row) {
            $data[] = array($config->determineOmittedNamespace($row['@']['module'], true), $row['file']);
        }
        return $data;
    }
}
