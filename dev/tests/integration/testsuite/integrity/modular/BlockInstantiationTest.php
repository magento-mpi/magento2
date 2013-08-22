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

/**
 * This test ensures that all blocks have the appropriate constructor arguments that allow
 * them to be instantiated via the objectManager.
 *
 * @magentoAppIsolation
 */
class Integrity_Modular_BlockInstantiationTest extends Magento_Test_TestCase_IntegrityAbstract
{
    /**
     * @param string $module
     * @param string $class
     * @param string $area
     * @dataProvider allBlocksDataProvider
     */
    public function testBlockInstantiation($module, $class, $area)
    {
        $this->assertNotEmpty($module);
        $this->assertTrue(class_exists($class), "Block class: {$class}");
        Magento_Test_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_Config_Scope')
            ->setCurrentScope($area);
        $block = Mage::getModel($class);
        $this->assertNotNull($block);
    }

    /**
     * @return array
     */
    public function allBlocksDataProvider()
    {
        $blockClass = '';
        $skipBlocks = array(
            // blocks with abstract constructor arguments
            'Magento_Adminhtml_Block_System_Email_Template',
            'Magento_Adminhtml_Block_System_Email_Template_Edit',
            'Magento_Backend_Block_System_Config_Edit',
            'Magento_Backend_Block_System_Config_Form',
            'Magento_Backend_Block_System_Config_Tabs',
            'Magento_Review_Block_Form',
            // Fails because of of bug in Magento_Webapi_Model_Acl_Loader_Resource_ConfigReader constructor
            'Magento_Adminhtml_Block_Cms_Page',
            'Magento_Adminhtml_Block_Cms_Page_Edit',
            'Magento_Adminhtml_Block_Sales_Order',
            'Magento_Oauth_Block_Adminhtml_Oauth_Consumer',
            'Magento_Oauth_Block_Adminhtml_Oauth_Consumer_Grid',
            'Magento_Paypal_Block_Adminhtml_Settlement_Report',
            'Magento_Sales_Block_Adminhtml_Billing_Agreement_View',
            'Magento_User_Block_Role_Tab_Edit',
            'Magento_Webapi_Block_Adminhtml_Role_Edit_Tab_Resource',
        );

        try {
            /** @var $website Magento_Core_Model_Website */
            Mage::app()->getStore()->setWebsiteId(0);

            $templateBlocks = array();
            $blockMods = Utility_Classes::collectModuleClasses('Block');
            foreach ($blockMods as $blockClass => $module) {
                if (!in_array($module, $this->_getEnabledModules())) {
                    continue;
                }
                if (in_array($blockClass, $skipBlocks)) {
                    continue;
                }
                $class = new ReflectionClass($blockClass);
                if ($class->isAbstract() || !$class->isSubclassOf('Magento_Core_Block_Template')) {
                    continue;
                }
                $templateBlocks = $this->_addBlock($module, $blockClass, $class, $templateBlocks);
            }
            return $templateBlocks;
        } catch (Exception $e) {
            trigger_error("Corrupted data provider. Last known block instantiation attempt: '{$blockClass}'."
                . " Exception: {$e}", E_USER_ERROR);
        }
    }

    /**
     * @param $module
     * @param $blockClass
     * @param $class
     * @param $templateBlocks
     * @return mixed
     */
    private function _addBlock($module, $blockClass, $class, $templateBlocks)
    {
        $area = 'frontend';
        if ($module == 'Magento_Install') {
            $area = 'install';
        } elseif ($module == 'Magento_Adminhtml' || strpos($blockClass, '_Adminhtml_')
            || strpos($blockClass, '_Backend_')
            || $class->isSubclassOf('Magento_Backend_Block_Template')
        ) {
            $area = 'adminhtml';
        }
        Mage::app()->loadAreaPart(
            Magento_Core_Model_App_Area::AREA_ADMINHTML,
            Magento_Core_Model_App_Area::PART_CONFIG
        );
        $templateBlocks[$module . ', ' . $blockClass . ', ' . $area]
            = array($module, $blockClass, $area);
        return $templateBlocks;
    }
}
