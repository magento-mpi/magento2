<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cartMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $qouteFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressFactoryMock;

    /**
     * @var \Magento\AdvancedCheckout\Model\Observer
     */
    protected $subject;

    protected function setUp()
    {
        $this->quoteMock = $this->getMock('\Magento\Sales\Model\Quote', [], [], '', false);
        $this->cartMock = $this->getMock('\Magento\AdvancedCheckout\Model\Cart', [], [], '', false);
        $this->collFactoryMock = $this->getMock('\Magento\Framework\Data\CollectionFactory', [], [], '', false);
        $this->helperMock = $this->getMock('\Magento\AdvancedCheckout\Helper\Data', [], [], '', false);
        $this->qouteFactoryMock = $this->getMock('\Magento\Sales\Model\QuoteFactory', [], [], '', false);
        $this->addressFactoryMock = $this->getMock('\Magento\Sales\Model\Quote\AddressFactory', [], [], '', false);

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->subject = $objectManager->getObject(
            '\Magento\AdvancedCheckout\Model\Observer',
            [
                'quote' => $this->quoteMock,
                'cart' => $this->cartMock,
                'collectionFactory' => $this->collFactoryMock,
                'checkoutData' => $this->helperMock,
                'quoteFactory' => $this->qouteFactoryMock,
                'addressFactory' => $this->addressFactoryMock,
            ]
        );
    }

    public function testAddCartLinkIfBlockNotSidebarInstance()
    {
        $eventMock = $this->getMock('\Magento\Framework\Event', [], [], '', false);

        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $observerMock->expects($this->once())->method('getEvent')->willReturn($eventMock);

        $blockMock = $this->getMock('\Magento\Framework\View\Element\AbstractBlock', [], [], '', false);
        $eventMock->expects($this->once())->method('getBlock')->willReturn($blockMock);
        $this->cartMock->expects($this->never())->method('getFailedItems');

        $this->subject->addCartLink($observerMock);
    }

    public function testAddCartLinkIfFailedItemsCountIsZero()
    {
        $eventMock = $this->getMock('\Magento\Framework\Event', [], [], '', false);

        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $observerMock->expects($this->once())->method('getEvent')->willReturn($eventMock);

        $blockMock = $this->getMock('\Magento\Checkout\Block\Cart\Sidebar', ['setAllowCartLink'], [], '', false);
        $eventMock->expects($this->once())->method('getBlock')->willReturn($blockMock);
        $this->cartMock->expects($this->once())->method('getFailedItems')->willReturn([]);

        $blockMock->expects($this->never())->method('setAllowCartLink');
        $this->subject->addCartLink($observerMock);
    }

    public function testAddCartLink()
    {
        $eventMock = $this->getMock('\Magento\Framework\Event', [], [], '', false);

        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $observerMock->expects($this->once())->method('getEvent')->willReturn($eventMock);

        $blockMock = $this->getMock(
            '\Magento\Checkout\Block\Cart\Sidebar',
            ['setAllowCartLink', 'setCartEmptyMessage'],
            [],
            '',
            false
        );
        $eventMock->expects($this->once())->method('getBlock')->willReturn($blockMock);
        $this->cartMock->expects($this->once())->method('getFailedItems')->willReturn(['item1', 'item2']);

        $blockMock->expects($this->once())->method('setAllowCartLink')->with(true)->willReturnSelf();
        $blockMock->expects($this->once())
            ->method('setCartEmptyMessage')
            ->with('2 item(s) need your attention.')
            ->willReturnSelf();

        $this->subject->addCartLink($observerMock);
    }
}
 