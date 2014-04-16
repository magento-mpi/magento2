<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Block\Catalog\Product;

/**
 * Tests Magento\Downloadable\Block\Catalog\Product\Links
 */
class LinksTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Downloadable\Block\Catalog\Product\Links */
    protected $linksBlock;

    /**
     * @var \Magento\Downloadable\Model\Link|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $linkMock;

    /**
     * @var \Magento\Downloadable\Pricing\Price\LinkPrice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $linkPriceMock;

    /**
     * @var \Magento\Pricing\Amount\Base|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $amountMock;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $salableItemMock;

    /**
     * @var \Magento\Pricing\PriceInfoInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceInfoMock;

    /**
     * @var \Magento\View\LayoutInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layout;

    public function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $contextMock = $this->getMock('Magento\Catalog\Block\Product\Context', [], [], '', false, false);

        $this->priceInfoMock = $this->getMock('Magento\Pricing\PriceInfo\Base', [], [], '', false);
        $this->linkPriceMock = $this->getMock('Magento\Downloadable\Pricing\Price\LinkPrice', [], [], '', false);
        $this->salableItemMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $this->amountMock = $this->getMock('Magento\Pricing\Amount\Base', [], [], '', false);
        $this->linkMock = $this->getMock('Magento\Downloadable\Model\Link', [], [], '', false);
        $this->layout = $this->getMock('Magento\View\Layout', [], [], '', false);
        $contextMock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($this->layout));
        $data = [
            'product' => $this->salableItemMock
        ];

        $this->linksBlock = $objectManager->getObject(
            'Magento\Downloadable\Block\Catalog\Product\Links',
            [
                'context' => $contextMock,
                'data' => $data
            ]
        );
    }

    public function testGetLinkPrice()
    {
        $priceCode = 'link_price';
        $arguments = [];
        $expectedHtml = 'some html';
        $this->salableItemMock->expects($this->any())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfoMock));
        $this->priceInfoMock->expects($this->any())
            ->method('getPrice')
            ->with($this->equalTo($priceCode))
            ->will($this->returnValue($this->linkPriceMock));
        $this->linkPriceMock->expects($this->any())
            ->method('getLinkAmount')
            ->with($this->linkMock)
            ->will($this->returnValue($this->amountMock));

        $priceBoxMock = $this->getMock('Magento\Pricing\Render', ['renderAmount'], [], '', false, false);

        $this->layout->expects($this->once())
            ->method('getBlock')
            ->with($this->equalTo('product.price.render.default'))
            ->will($this->returnValue($priceBoxMock));

        $priceBoxMock->expects($this->once())
            ->method('renderAmount')
            ->with($this->amountMock, $this->linkPriceMock, $this->salableItemMock, $arguments)
            ->will($this->returnValue($expectedHtml));

        $result = $this->linksBlock->getLinkPrice($this->linkMock);
        $this->assertEquals($expectedHtml, $result);
    }
}
 