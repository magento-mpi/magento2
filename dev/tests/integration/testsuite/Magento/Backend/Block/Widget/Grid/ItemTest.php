<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Backend\Block\Widget\Grid;

/**
 * @magentoAppArea adminhtml
 */
class ItemTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAdditionalActionBlock()
    {
        /** @var $layout \Magento\Framework\View\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        );
        /** @var $block \Magento\Backend\Block\Widget\Grid\Massaction\Item */
        $block = $layout->createBlock('Magento\Backend\Block\Widget\Grid\Massaction\Item', 'block');
        $expected = $layout->addBlock('Magento\Framework\View\Element\Template', 'additional_action', 'block');
        $this->assertSame($expected, $block->getAdditionalActionBlock());
    }
}
