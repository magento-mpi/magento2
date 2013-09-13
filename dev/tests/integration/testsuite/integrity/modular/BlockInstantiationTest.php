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

        /** @var Magento_Core_Model_App $app */
        $app = Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_App');
        $app->loadArea($area);

        $block = Mage::getModel($class);
        $this->assertNotNull($block);
    }

    /**
     * @return array
     */
    public function allBlocksDataProvider()
    {
        $blockClass = '';
        try {
            /** @var $website Magento_Core_Model_Website */
            Mage::app()->getStore()->setWebsiteId(0);

            $enabledModules = $this->_getEnabledModules();
            $skipBlocks = $this->_getBlocksToSkip();
            $templateBlocks = array();
            $blockMods = Utility_Classes::collectModuleClasses('Block');
            foreach ($blockMods as $blockClass => $module) {
                if (!isset($enabledModules[$module]) || isset($skipBlocks[$blockClass])) {
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
     * Loads block classes, that should not be instantiated during the instantiation test
     *
     * @return array
     */
    protected function _getBlocksToSkip()
    {
        $result = array();
        foreach (glob(__DIR__ . '/_files/skip_blocks*.php') as $file) {
            $blocks = include $file;
            $result = array_merge($result, $blocks);
        }
        return array_combine($result, $result);
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
