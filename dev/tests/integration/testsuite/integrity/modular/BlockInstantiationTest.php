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
        Mage::getConfig()->setCurrentAreaCode($area);
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
            'Mage_Adminhtml_Block_System_Email_Template',
            'Mage_Adminhtml_Block_System_Email_Template_Edit',
            'Mage_Backend_Block_System_Config_Edit',
            'Mage_Backend_Block_System_Config_Form',
            'Mage_Backend_Block_System_Config_Tabs',
            // Fails because of of bug in Mage_Webapi_Model_Acl_Loader_Resource_ConfigReader constructor
            'Mage_Adminhtml_Block_Cms_Page',
            'Mage_Adminhtml_Block_Cms_Page_Edit',
            'Mage_Adminhtml_Block_Sales_Order',
            'Mage_Oauth_Block_Adminhtml_Oauth_Consumer',
            'Mage_Oauth_Block_Adminhtml_Oauth_Consumer_Grid',
            'Mage_Paypal_Block_Adminhtml_Settlement_Report',
            'Mage_Sales_Block_Adminhtml_Billing_Agreement_View',
            'Mage_User_Block_Role_Tab_Edit',
            'Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_Resource',
            // Fails only in SAAS, could be something wrong list of classes being deleted
            'Enterprise_Cms_Block_Adminhtml_Cms_Page_Revision_Edit',
            'Mage_Adminhtml_Block_Sales_Order_Invoice_View',
            'Mage_AdminNotification_Block_Window',
            'Saas_Launcher_Block_Adminhtml_Storelauncher_Payments_Drawer',
            'Saas_Launcher_Block_Adminhtml_Storelauncher_WelcomeScreen',
        );

        try {
            /** @var $website Mage_Core_Model_Website */
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
                if ($class->isAbstract() || !$class->isSubclassOf('Mage_Core_Block_Template')) {
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
        if ($module == 'Mage_Install') {
            $area = 'install';
        } elseif ($module == 'Mage_Adminhtml' || strpos($blockClass, '_Adminhtml_')
            || strpos($blockClass, '_Backend_')
            || $class->isSubclassOf('Mage_Backend_Block_Template')
        ) {
            $area = 'adminhtml';
        }
        Mage::app()->loadAreaPart(
            Mage_Core_Model_App_Area::AREA_ADMINHTML,
            Mage_Core_Model_App_Area::PART_CONFIG
        );
        $templateBlocks[$module . ', ' . $blockClass . ', ' . $area]
            = array($module, $blockClass, $area);
        return $templateBlocks;
    }
}
