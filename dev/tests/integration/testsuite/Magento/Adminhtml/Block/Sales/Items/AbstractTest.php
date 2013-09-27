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
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout');
        /** @var $block Magento_Adminhtml_Block_Sales_Items_Abstract */
        $block = $layout->createBlock('Magento_Adminhtml_Block_Sales_Items_Abstract', 'block');

        $item = new Magento_Object;

        $this->assertEmpty($block->getItemExtraInfoHtml($item));

        $expectedHtml ='<html><body>some data</body></html>';
        /** @var $childBlock Magento_Core_Block_Text */
        $childBlock = $layout->addBlock('Magento_Core_Block_Text', 'other_block', 'block', 'order_item_extra_info');
        $childBlock->setText($expectedHtml);

        $this->assertEquals($expectedHtml, $block->getItemExtraInfoHtml($item));
        $this->assertSame($item, $childBlock->getItem());
    }
}
