<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Sales_Items_AbstractTest extends PHPUnit_Framework_TestCase
{
    public function testGetItemExtraInfoHtml()
    {
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = Mage::getSingleton('Magento\Core\Model\Layout');
        /** @var $block \Magento\Adminhtml\Block\Sales\Items\AbstractItems */
        $block = $layout->createBlock('Magento\Adminhtml\Block\Sales\Items\AbstractItems', 'block');

        $item = new \Magento\Object;

        $this->assertEmpty($block->getItemExtraInfoHtml($item));

        $expectedHtml ='<html><body>some data</body></html>';
        /** @var $childBlock \Magento\Core\Block\Text */
        $childBlock = $layout->addBlock('Magento\Core\Block\Text', 'other_block', 'block', 'order_item_extra_info');
        $childBlock->setText($expectedHtml);

        $this->assertEquals($expectedHtml, $block->getItemExtraInfoHtml($item));
        $this->assertSame($item, $childBlock->getItem());
    }
}
