<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_ConfigTest extends Mage_Backend_Area_TestCase
{
    /**
     * @covers Mage_Backend_Model_Config::save
     * @param array $groups
     * @magentoDbIsolation enabled
     * @dataProvider saveWithSingleStoreModeEnabledDataProvider
     * @magentoConfigFixture current_store general/single_store_mode/enabled 1
     */
    public function testSaveWithSingleStoreModeEnabled($groups)
    {
        Mage::getConfig()->setCurrentAreaCode('adminhtml');
        /** @var $_configDataObject Mage_Backend_Model_Config */
        $_configDataObject = Mage::getModel('Mage_Backend_Model_Config');
        $_configData = $_configDataObject->setSection('dev')
            ->setWebsite('base')
            ->load();
        $this->assertEmpty($_configData);

        $_configDataObject = Mage::getModel('Mage_Backend_Model_Config');
        $_configDataObject->setSection('dev')
            ->setGroups($groups)
            ->save();

        Mage::getConfig()->reinit();
        Mage::app()->reinitStores();

        /** @var $_configDataObject Mage_Backend_Model_Config */
        $_configDataObject = Mage::getModel('Mage_Backend_Model_Config');
        $_configDataObject->setSection('dev')
            ->setWebsite('base');

        $_configData = $_configDataObject->load();
        $this->assertArrayHasKey('dev/debug/template_hints', $_configData);
        $this->assertArrayHasKey('dev/debug/template_hints_blocks', $_configData);

        $_configDataObject = Mage::getModel('Mage_Backend_Model_Config');
        $_configDataObject->setSection('dev');
        $_configData = $_configDataObject->load();
        $this->assertArrayNotHasKey('dev/debug/template_hints', $_configData);
        $this->assertArrayNotHasKey('dev/debug/template_hints_blocks', $_configData);
    }

    public function saveWithSingleStoreModeEnabledDataProvider()
    {
        return require(__DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'config_groups.php');
    }

    /**
     * @covers Mage_Backend_Model_Config::save
     * @param string $section
     * @param array $groups
     * @param array $expected
     * @magentoDbIsolation enabled
     * @dataProvider saveDataProvider
     */
    public function testSave($section, $groups, $expected)
    {
        $this->markTestIncomplete('Bug: MAGETWO-10203');
        /** @var $_configDataObject Mage_Backend_Model_Config */
        $_configDataObject = Mage::getModel('Mage_Backend_Model_Config');
        $_configDataObject->setSection($section)
            ->setGroups($groups)
            ->save();

        foreach ($expected as $group => $expectedData) {
            $_configDataObject = Mage::getModel('Mage_Backend_Model_Config');
            $_configData = $_configDataObject->setSection($group)
                ->load();
            if (array_key_exists('payment/payflow_link/pwd', $_configData)) {
                $_configData['payment/payflow_link/pwd'] = Mage::helper('Mage_Core_Helper_Data')
                    ->decrypt($_configData['payment/payflow_link/pwd']);
            }
            $this->assertEquals($expectedData, $_configData);
        }
    }

    public function saveDataProvider()
    {
        return require(__DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'config_section.php');
    }
}
