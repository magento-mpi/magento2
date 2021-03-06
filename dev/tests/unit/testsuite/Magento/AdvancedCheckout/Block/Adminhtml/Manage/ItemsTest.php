<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Items test
 */
class ItemsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdvancedCheckout\Block\Adminhtml\Manage\Items
     */
    protected $block;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\View\Layout
     */
    protected $layoutMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Backend\Block\Template
     */
    protected $priceRenderBlock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Sales\Model\Quote\Item  */
    protected $itemMock;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * Initialize required data
     */
    protected function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->priceRenderBlock = $this->getMockBuilder('\Magento\Backend\Block\Template')
            ->disableOriginalConstructor()
            ->setMethods(['setItem', 'toHtml'])
            ->getMock();

        $this->layoutMock = $this->getMockBuilder('\Magento\Framework\View\Layout')
            ->disableOriginalConstructor()
            ->getMock();

        $this->itemMock = $this->getMockBuilder('\Magento\Sales\Model\Quote\Item')
            ->disableOriginalConstructor()
            ->getMock();

        $this->block = $this->objectManager->getObject(
            'Magento\AdvancedCheckout\Block\Adminhtml\Manage\Items',
            [
                'context' => $this->objectManager->getObject(
                        'Magento\Backend\Block\Template\Context',
                        ['layout' => $this->layoutMock]
                    )
            ]
        );
    }

    public function testGetItemUnitPriceHtml()
    {
        $html = '$34.28';

        $this->layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('item_unit_price')
            ->will($this->returnValue($this->priceRenderBlock));

        $this->priceRenderBlock->expects($this->once())
            ->method('setItem')
            ->with($this->itemMock);

        $this->priceRenderBlock->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue($html));


        $this->assertEquals($html, $this->block->getItemUnitPriceHtml($this->itemMock));
    }

    public function testGetItemRowTotalHtml()
    {
        $html = '$34.28';

        $this->layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('item_row_total')
            ->will($this->returnValue($this->priceRenderBlock));

        $this->priceRenderBlock->expects($this->once())
            ->method('setItem')
            ->with($this->itemMock);

        $this->priceRenderBlock->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue($html));


        $this->assertEquals($html, $this->block->getItemRowTotalHtml($this->itemMock));
    }
    public function testGetItemRowTotalWithDiscountHtml()
    {
        $html = '$34.28';

        $this->layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('item_row_total_with_discount')
            ->will($this->returnValue($this->priceRenderBlock));

        $this->priceRenderBlock->expects($this->once())
            ->method('setItem')
            ->with($this->itemMock);

        $this->priceRenderBlock->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue($html));


        $this->assertEquals($html, $this->block->getItemRowTotalWithDiscountHtml($this->itemMock));
    }
}
