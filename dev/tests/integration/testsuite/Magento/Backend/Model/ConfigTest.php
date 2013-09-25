<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Backend_Model_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Magento_Backend_Model_Config::save
     * @param array $groups
     * @magentoDbIsolation enabled
     * @dataProvider saveWithSingleStoreModeEnabledDataProvider
     * @magentoConfigFixture current_store general/single_store_mode/enabled 1
     */
    public function testSaveWithSingleStoreModeEnabled($groups)
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Config_Scope')
            ->setCurrentScope(Magento_Core_Model_App_Area::AREA_ADMINHTML);
        /** @var $_configDataObject Magento_Backend_Model_Config */
        $_configDataObject = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Backend_Model_Config');
        $_configData = $_configDataObject->setSection('dev')
            ->setWebsite('base')
            ->load();
        $this->assertEmpty($_configData);

        $_configDataObject = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Backend_Model_Config');
        $_configDataObject->setSection('dev')
            ->setGroups($groups)
            ->save();

        /** @var $_configDataObject Magento_Backend_Model_Config */
        $_configDataObject = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Backend_Model_Config');
        $_configDataObject->setSection('dev')
            ->setWebsite('base');

        $_configData = $_configDataObject->load();
        $this->assertArrayHasKey('dev/debug/template_hints', $_configData);
        $this->assertArrayHasKey('dev/debug/template_hints_blocks', $_configData);

        $_configDataObject = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Backend_Model_Config');
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
     * @covers Magento_Backend_Model_Config::save
     * @param string $section
     * @param array $groups
     * @param array $expected
     * @magentoDbIsolation enabled
     * @dataProvider saveDataProvider
     */
    public function testSave($section, $groups, $expected)
    {
        /** @var $_configDataObject Magento_Backend_Model_Config */
        $_configDataObject = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Backend_Model_Config');
        $_configDataObject->setSection($section)
            ->setWebsite('base')
            ->setGroups($groups)
            ->save();

        foreach ($expected as $group => $expectedData) {
            $_configDataObject = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
                ->create('Magento_Backend_Model_Config');
            $_configData = $_configDataObject->setSection($group)->setWebsite('base')
                ->load();
            if (array_key_exists('payment/payflow_link/pwd', $_configData)) {
                $_configData['payment/payflow_link/pwd'] =
                    Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Helper_Data')
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
