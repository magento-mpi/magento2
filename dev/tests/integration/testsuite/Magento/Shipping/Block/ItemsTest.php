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

namespace Magento\Shipping\Block;

class ItemsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCommentsHtml()
    {
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface');
        $block = $layout->createBlock('Magento\Shipping\Block\Items', 'block');
        $childBlock = $layout->addBlock('Magento\View\Element\Text', 'shipment_comments', 'block');
        $shipment = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Order\Shipment');

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
