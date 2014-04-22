<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Pricing\Price;

/**
 * Class LinkPriceTest
 * @package Magento\Downloadable\Pricing\Price
 */
class LinkPriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Downloadable\Pricing\Price\LinkPrice
     */
    protected $linkPrice;

    /**
     * @var \Magento\Pricing\Amount\Base|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $amountMock;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $salableItemMock;

    /**
     * @var \Magento\Pricing\Adjustment\Calculator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $calculatorMock;

    /**
     * @var \Magento\Downloadable\Model\Resource\Link|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $linkMock;

    /**
     * Test setUp
     */
    protected function setUp()
    {
        $this->salableItemMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $this->amountMock = $this->getMock('Magento\Pricing\Amount\Base', [], [], '', false);
        $this->calculatorMock = $this->getMock('Magento\Pricing\Adjustment\Calculator', [], [], '', false);
        $this->linkMock = $this->getMock(
            'Magento\Downloadable\Model\Link',
            ['getPrice', 'getProduct', '__wakeup'],
            [],
            '',
            false
        );

        $this->linkPrice = new LinkPrice($this->salableItemMock, 1, $this->calculatorMock);
    }

    public function testGetLinkAmount()
    {
        $amount = 100;

        $this->linkMock->expects($this->once())
            ->method('getPrice')
            ->will($this->returnValue($amount));
        $this->linkMock->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($this->salableItemMock));
        $this->calculatorMock->expects($this->once())
            ->method('getAmount')
            ->with($amount, $this->equalTo($this->salableItemMock))
            ->will($this->returnValue($amount));

        $result = $this->linkPrice->getLinkAmount($this->linkMock);
        $this->assertEquals($amount, $result);
    }

} 