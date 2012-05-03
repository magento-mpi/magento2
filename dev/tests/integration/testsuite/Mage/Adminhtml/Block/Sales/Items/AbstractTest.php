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

class Mage_Adminhtml_Block_Sales_Items_AbstractTest extends PHPUnit_Framework_TestCase
{
    public function testGetItemExtraInfoHtml()
    {
        $layout = new Mage_Core_Model_Layout();
        $block = $this->getMockForAbstractClass('Mage_Adminhtml_Block_Sales_Items_Abstract');
        $layout->addBlock($block, 'block');

        $item = new Varien_Object;

        $this->assertEmpty($block->getItemExtraInfoHtml($item));

        $expectedHtml ='<html><body>some data</body></html>';
        $childBlock = $layout->addBlock('Mage_Core_Block_Text', 'other_block', 'block', 'order_item_extra_info');
        $childBlock->setText($expectedHtml);

        $this->assertEquals($expectedHtml, $block->getItemExtraInfoHtml($item));
        $this->assertSame($item, $childBlock->getItem());
    }
}
