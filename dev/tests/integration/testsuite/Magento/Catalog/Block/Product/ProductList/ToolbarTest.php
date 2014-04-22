<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Product\ProductList;

class ToolbarTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPagerHtml()
    {
        /** @var $layout \Magento\Framework\View\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        );
        /** @var $block \Magento\Catalog\Block\Product\ProductList\Toolbar */
        $block = $layout->createBlock('Magento\Catalog\Block\Product\ProductList\Toolbar', 'block');
        /** @var $childBlock \Magento\Framework\View\Element\Text */
        $childBlock = $layout->addBlock('Magento\Framework\View\Element\Text', 'product_list_toolbar_pager', 'block');

        $expectedHtml = '<b>Any text there</b>';
        $this->assertNotEquals($expectedHtml, $block->getPagerHtml());
        $childBlock->setText($expectedHtml);
        $this->assertEquals($expectedHtml, $block->getPagerHtml());
    }
}
