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

namespace Magento\GoogleShopping\Block\Adminhtml\Items;

/**
 * @magentoAppArea adminhtml
 */
class ProductTest extends \PHPUnit_Framework_TestCase
{
    public function testBeforeToHtml()
    {
        $this->markTestIncomplete('Magento_GoogleShopping is not implemented yet');

        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout')
            ->createBlock('Magento\GoogleShopping\Block\Adminhtml\Items\Product');
        $filter = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout')
            ->createBlock('Magento\Core\Block\Text');
        $search = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout')
            ->createBlock('Magento\Core\Block\Text');

        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout');
        $layout->addBlock($block, 'product');
        $layout->addBlock($filter, 'reset_filter_button', 'product');
        $layout->addBlock($search, 'search_button', 'product');
        $block->toHtml();

        $this->assertEquals('googleshopping_selection_search_grid_JsObject.resetFilter()', $filter->getData('onclick'));
        $this->assertEquals('googleshopping_selection_search_grid_JsObject.doFilter()', $search->getData('onclick'));
    }
}
