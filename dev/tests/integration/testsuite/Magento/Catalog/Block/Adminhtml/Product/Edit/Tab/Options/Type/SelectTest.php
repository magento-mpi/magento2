<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Type;

/**
 * @magentoAppArea adminhtml
 */
class SelectTest extends \PHPUnit_Framework_TestCase
{
    public function testToHtmlFormId()
    {
        /** @var $layout \Magento\Framework\View\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        );
        /** @var $block \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Type\Select */
        $block = $layout->createBlock(
            'Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Type\Select',
            'select'
        );
        $html = $block->getPriceTypeSelectHtml();
        $this->assertContains('select_${select_id}', $html);
        $this->assertContains('[${select_id}]', $html);
    }
}
