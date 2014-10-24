<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Block\Adminhtml\Sales\Items\Column\Name;

class GiftcardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftCard\Block\Adminhtml\Sales\Items\Column\Name\Giftcard
     */
    protected $block;

    /**
     * @var \Magento\Framework\Escaper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $escaper;

    public function setUp()
    {
        $this->escaper = $this->getMockBuilder('Magento\Framework\Escaper')
            ->setMethods(['escapeHtml'])
            ->disableOriginalConstructor()
            ->getMock();

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->block = $objectManagerHelper->getObject(
            'Magento\GiftCard\Block\Adminhtml\Sales\Items\Column\Name\Giftcard',
            [
                'escaper' => $this->escaper,
            ]
        );
    }

    public function testGetOrderOptions()
    {
        $expectedResult = array(
            array(
                'label' => 'Gift Card Type',
                'value' => 'Physical',
            ),
            array(
                'label' => 'Gift Card Sender',
                'value' => 'sender_name &lt;sender_email&gt;',
                'custom_view' => true,
            ),
            array(
                'label' => 'Gift Card Recipient',
                'value' => 'recipient_name &lt;recipient_email&gt;',
                'custom_view' => true,
            ),
            array(
                'label' => 'Gift Card Message',
                'value' => 'giftcard_message',
            ),
            array(
                'label' => 'Gift Card Lifetime',
                'value' => 'lifetime days',
            ),
            array(
                'label' => 'Gift Card Is Redeemable',
                'value' => 'Yes',
            ),
            array(
                'label' => 'Gift Card Accounts',
                'value' => 'xxx123<br />yyy456<br />N/A<br />N/A<br />N/A',
                'custom_view' => true,
            ),
        );

        $itemMock = $this->getMockBuilder('\Magento\Sales\Model\Order\Item')
            ->setMethods(['getProductOptionByCode', 'getQtyOrdered'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->block->setData('item', $itemMock);
        $itemMock->expects($this->at(0))
            ->method('getProductOptionByCode')
            ->with('giftcard_type')
            ->willReturn('1');
        $this->prepareCustomOptionMock($itemMock, 'giftcard_sender_name', 'sender_name', 1, 0);
        $this->prepareCustomOptionMock($itemMock, 'giftcard_sender_email', 'sender_email', 2, 1);
        $this->prepareCustomOptionMock($itemMock, 'giftcard_recipient_name', 'recipient_name', 3, 2);
        $this->prepareCustomOptionMock($itemMock, 'giftcard_recipient_email', 'recipient_email', 4, 3);
        $this->prepareCustomOptionMock($itemMock, 'giftcard_message', 'giftcard_message', 5, 4);
        $this->prepareCustomOptionMock($itemMock, 'giftcard_lifetime', 'lifetime', 6, 5);
        $this->prepareCustomOptionMock($itemMock, 'giftcard_is_redeemable', 1, 7, 6);
        $itemMock->expects($this->once())
            ->method('getQtyOrdered')
            ->willReturn(5);
        $itemMock->expects($this->at(9))
            ->method('getProductOptionByCode')
            ->with('giftcard_created_codes')
            ->willReturn(['xxx123', 'yyy456']);

        $this->assertEquals($expectedResult, $this->block->getOrderOptions());
    }

    /**
     * @param $itemMock
     * @param $code
     * @param $result
     * @param $itemIndex
     * @param $escaperIndex
     * @return mixed
     */
    private function prepareCustomOptionMock($itemMock, $code, $result, $itemIndex, $escaperIndex)
    {
        $this->block->setData('item', $itemMock);

        $itemMock->expects($this->at($itemIndex))
            ->method('getProductOptionByCode')
            ->with($code)
            ->willReturn('some_option');

        $this->escaper->expects($this->at($escaperIndex))
            ->method('escapeHtml')
            ->with('some_option')
            ->willReturn($result);

        return $result;
    }
}
