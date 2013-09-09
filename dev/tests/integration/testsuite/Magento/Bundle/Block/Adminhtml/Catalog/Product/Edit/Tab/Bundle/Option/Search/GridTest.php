<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search_GridTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtmlHasOnClick()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface')
            ->setArea(Magento_Core_Model_App_Area::AREA_ADMINHTML);
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getModel(
            'Magento_Core_Model_Layout',
            array('area' => Magento_Core_Model_App_Area::AREA_ADMINHTML)
        );
        $block = $layout->createBlock(
            'Magento_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search_Grid',
            'block');
        $block->setId('temp_id');

        $html = $block->toHtml();

        $regexpTemplate = '/<button [^>]* onclick="temp_id[^"]*\\.%s/i';
        $jsFuncs = array('doFilter', 'resetFilter');
        foreach ($jsFuncs as $func) {
            $regexp = sprintf($regexpTemplate, $func);
            $this->assertRegExp($regexp, $html);
        }
    }
}
