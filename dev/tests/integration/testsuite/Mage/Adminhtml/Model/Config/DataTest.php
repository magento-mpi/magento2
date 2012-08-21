<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Adminhtml
 */
class Mage_Adminhtml_Model_Config_DataTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $this->assertInstanceOf('Mage_Adminhtml_Model_Config_Data', Mage::getModel('Mage_Adminhtml_Model_Config_Data'));
    }

    /**
     * @param array $groups
     * @magentoDbIsolation enabled
     * @dataProvider saveDataProvider
     */
    public function testSave($groups)
    {
        $_configData = Mage::getModel('Mage_Adminhtml_Model_Config_Data')
            ->setSection('dev')
            ->setWebsite('base')
            ->load();
        $this->assertEmpty($_configData);

        Mage::getModel('Mage_Adminhtml_Model_Config_Data')
            ->setSection('dev')
            ->setGroups($groups)
            ->save();

        Mage::getConfig()->reinit();
        Mage::app()->reinitStores();

        $_configDataObject = Mage::getModel('Mage_Adminhtml_Model_Config_Data')
            ->setSection('dev')
            ->setWebsite('base');

        $_configData = $_configDataObject->load();
        $this->assertArrayHasKey('dev/debug/template_hints', $_configData);
        $this->assertArrayHasKey('dev/debug/template_hints_blocks', $_configData);

        $_configDataObject = Mage::getModel('Mage_Adminhtml_Model_Config_Data')
            ->setSection('dev');
        $_configData = $_configDataObject->load();
        $this->assertArrayNotHasKey('dev/debug/template_hints', $_configData);
        $this->assertArrayNotHasKey('dev/debug/template_hints_blocks', $_configData);
    }

    public function saveDataProvider()
    {
        return require(__DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'config_groups.php');
    }
}
