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
    protected $quoteRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $giftCardBuilderMock;

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

        $this->quoteRepositoryMock = $this->getMock('\Magento\Sales\Model\QuoteRepository', [], [], '', false);
        $this->giftCardBuilderMock =
            $this->getMock('Magento\GiftCardAccount\Service\V1\Data\Cart\GiftCardAccountBuilder', [], [], '', false);
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
                'quoteRepository' => $this->quoteRepositoryMock,
                'giftCardBuilder' => $this->giftCardBuilderMock,
                'giftCardHelper' => $this->giftCardHelperMock
            ]
        );
    }

    public function testGetList()
    {
        $cartId= 12;

        $this->quoteRepositoryMock
            ->expects($this->once())
            ->method('get')
            ->with($cartId)
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
