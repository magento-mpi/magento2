<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\Items;

/**
 * @magentoAppArea adminhtml
 */
class AbstractTest extends \PHPUnit_Framework_TestCase
{
    public function testGetItemExtraInfoHtml()
    {
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface');
        /** @var $block \Magento\Sales\Block\Adminhtml\Items\AbstractItems */
        $block = $layout->createBlock('Magento\Sales\Block\Adminhtml\Items\AbstractItems', 'block');

        $item = new \Magento\Object();

        $this->assertEmpty($block->getItemExtraInfoHtml($item));

        $expectedHtml = '<html><body>some data</body></html>';
        /** @var $childBlock \Magento\View\Element\Text */
        $childBlock = $layout->addBlock('Magento\View\Element\Text', 'other_block', 'block', 'order_item_extra_info');
        $childBlock->setText($expectedHtml);

        $this->assertEquals($expectedHtml, $block->getItemExtraInfoHtml($item));
        $this->assertSame($item, $childBlock->getItem());
    }
}
