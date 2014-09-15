<?php
/** 
 * 
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Service\V1;

use \Magento\GiftCardAccount\Service\V1\Data\Cart\GiftCardAccount as GiftCardAccount;

class ReadServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReadService
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
    protected $storeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteMock;

    protected function setUp()
    {
        $objectManager =new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->quoteLoaderMock = $this->getMock('\Magento\Checkout\Service\V1\QuoteLoader', [], [], '', false);
        $this->giftCardBuilderMock =
            $this->getMock('Magento\GiftCardAccount\Service\V1\Data\Cart\GiftCardAccountBuilder', [], [], '', false);
        $this->storeManagerMock = $this->getMock('\Magento\Framework\StoreManagerInterface');
        $this->giftCardHelperMock = $this->getMock('\Magento\GiftCardAccount\Helper\Data', [], [], '', false);
        $this->storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->quoteMock = $this->getMock('\Magento\Sales\Model\Quote',
            [
                'getGiftCardsAmount',
                'getBaseGiftCardsAmount',
                'getGiftCardsAmountUsed',
                'getBaseGiftCardsAmountUsed',
                '__wakeup'

            ],
            [],
            '',
            false
        );

        $this->service = $objectManager->getObject(
            'Magento\GiftCardAccount\Service\V1\ReadService',
            [
                'quoteLoader' => $this->quoteLoaderMock,
                'giftCardBuilder' => $this->giftCardBuilderMock,
                'storeManager' => $this->storeManagerMock,
                'giftCardHelper' => $this->giftCardHelperMock
            ]
        );
    }

    public function testGetList()
    {
        $cartId= 12;
        $storeId = 34;

        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->quoteLoaderMock
            ->expects($this->once())
            ->method('load')
            ->with($cartId, $storeId)
            ->will($this->returnValue($this->quoteMock));
        $this->giftCardHelperMock
            ->expects($this->once())
            ->method('getCards')
            ->with($this->quoteMock)
            ->will($this->returnValue([['c' => 'ABC-123'], ['c' => 'DEF-098']]));
        $data = [
            GiftCardAccount::GIFT_CARDS => ['ABC-123', 'DEF-098'],
            GiftCardAccount::GIFT_CARDS_AMOUNT => 100,
            GiftCardAccount::BASE_GIFT_CARDS_AMOUNT => 90,
            GiftCardAccount::GIFT_CARDS_AMOUNT_USED => 50,
            GiftCardAccount::BASE_GIFT_CARDS_AMOUNT_USED => 40
        ];
        $this->quoteMock->expects($this->once())->method('getGiftCardsAmount')->will($this->returnValue(100));
        $this->quoteMock->expects($this->once())->method('getBaseGiftCardsAmount')->will($this->returnValue(90));
        $this->quoteMock->expects($this->once())->method('getGiftCardsAmountUsed')->will($this->returnValue(50));
        $this->quoteMock->expects($this->once())->method('getBaseGiftCardsAmountUsed')->will($this->returnValue(40));
        $this->giftCardBuilderMock
            ->expects($this->once())
            ->method('populateWithArray')
            ->with($data)
            ->will($this->returnValue($this->giftCardBuilderMock));
        $this->giftCardBuilderMock
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue('Expected Value'));

        $this->assertEquals('Expected Value', $this->service->getList($cartId));
    }
}
 