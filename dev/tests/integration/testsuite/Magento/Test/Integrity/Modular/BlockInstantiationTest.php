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

namespace Magento\Test\Integrity\Modular;

/**
 * This test ensures that all blocks have the appropriate constructor arguments that allow
 * them to be instantiated via the objectManager.
 *
 * @magentoAppIsolation
 */
class BlockInstantiationTest extends \Magento\TestFramework\TestCase\AbstractIntegrity
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
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Config\ScopeInterface')
            ->setCurrentScope($area);

        /** @var \Magento\Core\Model\App $app */
        $app = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App');
        $app->loadArea($area);

        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create($class);
        $this->assertNotNull($block);
    }

    /**
     * @return array
     */
    public function allBlocksDataProvider()
    {
        $blockClass = '';
        try {
            /** @var $website \Magento\Core\Model\Website */
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
                ->getStore()->setWebsiteId(0);

            $enabledModules = $this->_getEnabledModules();
            $skipBlocks = $this->_getBlocksToSkip();
            $templateBlocks = array();
            $blockMods = \Magento\TestFramework\Utility\Classes::collectModuleClasses('Block');
            foreach ($blockMods as $blockClass => $module) {
                if (!isset($enabledModules[$module]) || isset($skipBlocks[$blockClass])) {
                    continue;
                }
                $class = new \ReflectionClass($blockClass);
                if ($class->isAbstract() || !$class->isSubclassOf('Magento\Core\Block\Template')) {
                    continue;
                }
                $templateBlocks = $this->_addBlock($module, $blockClass, $class, $templateBlocks);
            }
            return $templateBlocks;
        } catch (\Exception $e) {
            trigger_error("Corrupted data provider. Last known block instantiation attempt: '{$blockClass}'."
                . " \Exception: {$e}", E_USER_ERROR);
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
        } elseif ($module == 'Magento_Adminhtml' || strpos($blockClass, '\\Adminhtml\\')
            || strpos($blockClass, '_Backend_')
            || $class->isSubclassOf('Magento\Backend\Block\Template')
        ) {
            $area = 'adminhtml';
        }
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')->loadAreaPart(
            \Magento\Core\Model\App\Area::AREA_ADMINHTML,
            \Magento\Core\Model\App\Area::PART_CONFIG
        );
        $templateBlocks[$module . ', ' . $blockClass . ', ' . $area]
            = array($module, $blockClass, $area);
        return $templateBlocks;
    }
}
