<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Sales_Block_Order_Shipment_ItemsTest extends PHPUnit_Framework_TestCase
{
    public function testGetCommentsHtml()
    {
        $layout = Mage::getModel('Magento_Core_Model_Layout');
        $block = $layout->createBlock('Mage_Sales_Block_Order_Shipment_Items', 'block');
        $childBlock = $layout->addBlock('Magento_Core_Block_Text', 'shipment_comments', 'block');
        $shipment = Mage::getModel('Mage_Sales_Model_Order_Shipment');

        $expectedHtml = '<b>Any html</b>';
        $this->assertEmpty($childBlock->getEntity());
        $this->assertEmpty($childBlock->getTitle());
        $this->assertNotEquals($expectedHtml, $block->getCommentsHtml($shipment));

        $childBlock->setText($expectedHtml);
        $actualHtml = $block->getCommentsHtml($shipment);
        $this->assertSame($shipment, $childBlock->getEntity());
        $this->assertNotEmpty($childBlock->getTitle());
        $this->assertEquals($expectedHtml, $actualHtml);
    }
}
