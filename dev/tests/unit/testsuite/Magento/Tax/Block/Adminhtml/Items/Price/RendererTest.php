<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Block\Adminhtml\Items\Price;

use Magento\Framework\Object;

class RendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tax\Block\Adminhtml\Items\Price\Renderer
     */
    protected $renderer;

    /**
     * @var \Magento\Tax\Block\Item\Price\Renderer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemPriceRenderer;

    /**
     * @var \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $defaultColumnRenderer;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->itemPriceRenderer = $this->getMockBuilder('\Magento\Tax\Block\Item\Price\Renderer')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'displayPriceInclTax',
                    'displayPriceExclTax',
                    'displayBothPrices',
                    'getTotalAmount',
                    'formatPrice',
                ]
            )
            ->getMock();

        $this->defaultColumnRenderer = $this->getMockBuilder(
            '\Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn'
        )->disableOriginalConstructor()
            ->setMethods(['displayPrices'])
            ->getMock();

        $this->renderer = $objectManager->getObject(
            '\Magento\Tax\Block\Adminhtml\Items\Price\Renderer',
            [
                'itemPriceRenderer' => $this->itemPriceRenderer,
                'defaultColumnRenderer' => $this->defaultColumnRenderer,
            ]
        );
    }

    public function testDisplayPriceInclTax()
    {
        $this->itemPriceRenderer->expects($this->once())
            ->method('displayPriceInclTax');

        $this->renderer->displayPriceInclTax();
    }

    public function testDisplayPriceExclTax()
    {
        $this->itemPriceRenderer->expects($this->once())
            ->method('displayPriceExclTax');

        $this->renderer->displayPriceExclTax();
    }

    public function testDisplayBothPrices()
    {
        $this->itemPriceRenderer->expects($this->once())
            ->method('displayBothPrices');

        $this->renderer->displayBothPrices();
    }

    public function testDisplayPrices()
    {
        $basePrice = 3;
        $price = 4;
        $display = "$3 [L4]";

        $this->defaultColumnRenderer->expects($this->once())
            ->method('displayPrices')
            ->with($basePrice, $price)
            ->will($this->returnValue($display));

        $this->assertEquals($display, $this->renderer->displayPrices($basePrice, $price));
    }

    public function testFormatPrice()
    {
        $price = 4;
        $display = "$3";

        $this->itemPriceRenderer->expects($this->once())
            ->method('formatPrice')
            ->with($price)
            ->will($this->returnValue($display));

        $this->assertEquals($display, $this->renderer->formatPrice($price));
    }

    public function testGetTotalAmount()
    {
        $itemMock = $this->getMockBuilder('\Magento\Sales\Model\Order\Item')
            ->disableOriginalConstructor()
            ->getMock();

        $this->itemPriceRenderer->expects($this->once())
            ->method('getTotalAmount')
            ->with($itemMock);

        $this->renderer->getTotalAmount($itemMock);
    }

}
