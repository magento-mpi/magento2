<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Banner\Block\Widget;

/**
 * @magentoAppArea frontend
 */
class BannerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testAddWidgetBanner()
    {
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        );
        /** @var \Magento\Banner\Block\Widget\Banner $block */
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Banner\Block\Widget\Banner'
        );
        $block->setTemplate('widget/block.phtml');
        $layout->addBlock($block, 'block1');
        $this->assertArrayHasKey('block1', $layout->getAllBlocks());
    }
}
