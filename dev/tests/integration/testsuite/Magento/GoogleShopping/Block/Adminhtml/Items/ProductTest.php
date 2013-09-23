<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_GoogleShopping_Block_Adminhtml_Items_ProductTest extends PHPUnit_Framework_TestCase
{
    public function testBeforeToHtml()
    {
        $this->markTestIncomplete('Magento_GoogleShopping is not implemented yet');

        $block = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout')
            ->createBlock('Magento_GoogleShopping_Block_Adminhtml_Items_Product');
        $filter = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout')
            ->createBlock('Magento_Core_Block_Text');
        $search = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout')
            ->createBlock('Magento_Core_Block_Text');

        $layout = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout');
        $layout->addBlock($block, 'product');
        $layout->addBlock($filter, 'reset_filter_button', 'product');
        $layout->addBlock($search, 'search_button', 'product');
        $block->toHtml();

        $this->assertEquals('googleshopping_selection_search_grid_JsObject.resetFilter()', $filter->getData('onclick'));
        $this->assertEquals('googleshopping_selection_search_grid_JsObject.doFilter()', $search->getData('onclick'));
    }
}
