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

class Magento_Test_Integrity_Theme_RenderersTest extends Magento_TestFramework_TestCase_IntegrityAbstract
{
    /**
     * @param string $module
     * @param string $xpath
     * @dataProvider rendererDeclarationsDataProvider
     */
    public function testRendererDeclarations($module, $xpath)
    {
        $this->_getEnabledModules();
        if (!isset($this->_enabledModules[$module])) {
            $this->markTestSkipped("The module '$module' is not available.");
        }

        $blocks = array();
        foreach ($this->_getDesignThemes() as $theme) {
            /** @var Magento_Core_Model_Layout_Merge $layoutUpdate */
            $layoutUpdate = Mage::getModel('Magento_Core_Model_Layout_Merge', array('theme' => $theme));
            $blockElements = $layoutUpdate->getFileLayoutUpdatesXml()->xpath($xpath);
            if ($blockElements) {
                foreach ($blockElements as $block) {
                    $blocks[] = (string)$block;
                }
            }
        }
        $blocks = array_unique($blocks);
        $this->assertNotEmpty($blocks, "There are no block declarations found by xpath '{$xpath}' (module {$module})");

        foreach ($blocks as $block) {
            $this->assertNotEmpty(
                Mage::app()->getLayout()->createBlock($block), "Failed to instantiate block '{$block}'"
            );
        }
    }

    /**
     * @return array
     */
    public function rendererDeclarationsDataProvider()
    {
        return array(
            array('Magento_CustomerCustomAttributes', '//action[@method=\'addRenderer\']/renderer_block'),
            array('Magento_Rma', '//action[@method=\'addRenderer\']/renderer_block'),
            array('Magento_Adminhtml', '//action[@method=\'addOptionRenderer\']/block'),
            array('Magento_Bundle', '//action[@method=\'addRenderer\']/block'),
            array('Magento_Catalog', '//action[@method=\'addOptionRenderer\']/block'),
        );
    }
}
