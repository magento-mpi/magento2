<?php
/** 
 * 
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Service\V1;

class WriteServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WriteService
     */
    protected $service;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteLoaderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $giftCardBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $giftCardHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $giftCardLoaderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $giftCardAccountData;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $giftCard;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $giftCardAccountMock;

    protected function setUp()
    {
        $objectManager =new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->quoteLoaderMock = $this->getMock('\Magento\Checkout\Service\V1\QuoteLoader', [], [], '', false);
        $this->giftCardBuilderMock =
            $this->getMock('Magento\GiftCardAccount\Service\V1\Data\Cart\GiftCardAccountBuilder', [], [], '', false);
        $this->storeManagerMock = $this->getMock('\Magento\Framework\StoreManagerInterface');
        $this->giftCardHelperMock = $this->getMock('\Magento\GiftCardAccount\Helper\Data', [], [], '', false);
        $this->storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->giftCardLoaderMock =
            $this->getMock('\Magento\GiftCardAccount\Service\V1\GiftCardAccountLoader', [], [], '', false);
        $this->giftCardAccountData =
            $this->getMock('\Magento\GiftCardAccount\Service\V1\Data\Cart\GiftCardAccount', [], [], '', false);
        $this->giftCard = $this->getMock('Magento\GiftCardAccount\Model\Giftcardaccount', [], [], '', false);
        $this->quoteMock = $this->getMock('\Magento\Sales\Model\Quote',
            [
                'getItemsCount',
                '__wakeup'
            ],
            [],
            '',
            false
        );

        $this->service = $objectManager->getObject(
            'Magento\GiftCardAccount\Service\V1\WriteService',
            [
                'quoteLoader' => $this->quoteLoaderMock,
                'giftCardBuilder' => $this->giftCardBuilderMock,
                'storeManager' => $this->storeManagerMock,
                'giftCardHelper' => $this->giftCardHelperMock,
                'loader' => $this->giftCardLoaderMock
            ]
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Cart 12 doesn't contain products
     */
    public function testSetWithNoSuchEntityException()
    {
        $cartId = 12;
        $storeId = 34;

        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->quoteLoaderMock
            ->expects($this->once())
            ->method('load')
            ->with($cartId, $storeId)
            ->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('getItemsCount')->will($this->returnValue(0));
        $this->giftCard->expects($this->never())->method('getGiftCards');

        $this->service->set($cartId, $this->giftCardAccountData);
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Could not add gift card code
     */
    public function testSetWithCouldNotSaveException()
    {
        $cartId = 12;
        $storeId = 34;
        $cardCode = [['ABC-123']];

        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->quoteLoaderMock
            ->expects($this->once())
            ->method('load')
            ->with($cartId, $storeId)
            ->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('getItemsCount')->will($this->returnValue(1));
        $this->giftCardAccountData->expects($this->once())
            ->method('getGiftCards')->will($this->returnValue($cardCode));
        $this->giftCardLoaderMock
            ->expects($this->once())
            ->method('load')
            ->with(array_shift($cardCode))
            ->will($this->returnValue($this->giftCard));
        $exception = new \Magento\Framework\Exception\CouldNotSaveException('Could not add gift card code');
        $this->giftCard
            ->expects($this->any())
            ->method('addToCart')
            ->with(true, $this->quoteMock)
            ->will($this->throwException($exception));

        $this->service->set($cartId, $this->giftCardAccountData);
    }

    public function testSet()
    {
        $cartId = 12;
        $storeId = 34;
        $cardCode = [['ABC-123']];

        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->quoteLoaderMock
            ->expects($this->once())
            ->method('load')
            ->with($cartId, $storeId)
            ->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('getItemsCount')->will($this->returnValue(1));
        $this->giftCardAccountData->expects($this->once())
            ->method('getGiftCards')->will($this->returnValue($cardCode));
        $this->giftCardLoaderMock
            ->expects($this->once())
            ->method('load')
            ->with(array_shift($cardCode))
            ->will($this->returnValue($this->giftCard));
        $this->giftCard
            ->expects($this->any())
            ->method('addToCart')
            ->with(true, $this->quoteMock)
            ->will($this->returnValue($this->giftCard));

        $this->assertTrue($this->service->set($cartId, $this->giftCardAccountData));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Cart 12 doesn't contain products
     */
    public function testDeleteWithNoSuchEntityException()
    {
        $cartId = 12;
        $storeId = 34;
        $couponCode = 'ABC-1223';

        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->quoteLoaderMock
            ->expects($this->once())
            ->method('load')
            ->with($cartId, $storeId)
            ->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('getItemsCount')->will($this->returnValue(0));

        $this->service->delete($cartId, $couponCode);
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     * @expectedExceptionMessage Could not delete gift card from quote
     */
    public function testDeleteWithCouldNotDeleteException()
    {
        $cartId = 12;
        $storeId = 34;
        $couponCode = 'ABC-1223';

        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->quoteLoaderMock
            ->expects($this->once())
            ->method('load')
            ->with($cartId, $storeId)
            ->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('getItemsCount')->will($this->returnValue(1));
        $this->giftCardLoaderMock
            ->expects($this->once())
            ->method('load')
            ->with($couponCode)
            ->will($this->returnValue($this->giftCard));
        $exception = new \Magento\Framework\Exception\CouldNotDeleteException('Could not delete gift card from quote');
        $this->giftCard
            ->expects($this->any())
            ->method('removeFromCart')
            ->with(true, $this->quoteMock)
            ->will($this->throwException($exception));

        $this->service->delete($cartId, $couponCode);
    }

    public function testDelete()
    {
        $cartId = 12;
        $storeId = 34;
        $couponCode = 'ABC-1223';

        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->quoteLoaderMock
            ->expects($this->once())
            ->method('load')
            ->with($cartId, $storeId)
            ->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('getItemsCount')->will($this->returnValue(1));
        $this->giftCardLoaderMock
            ->expects($this->once())
            ->method('load')
            ->with($couponCode)
            ->will($this->returnValue($this->giftCard));
        $this->giftCard
            ->expects($this->any())
            ->method('removeFromCart')
            ->with(true, $this->quoteMock);

        $this->assertTrue($this->service->delete($cartId, $couponCode));
    }
}
