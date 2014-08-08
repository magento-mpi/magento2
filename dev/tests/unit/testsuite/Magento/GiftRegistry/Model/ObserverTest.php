<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * GiftRegistry observer
     *
     * @var \Magento\GiftRegistry\Model\Observer
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    protected function setUp()
    {
        $this->helperMock = $this->getMock('\Magento\GiftRegistry\Helper\Data', [], [], '', false);
        $designMock = $this->getMock('\Magento\Framework\View\DesignInterface');
        $sessionMock = $this->getMock('\Magento\Customer\Model\Session', [], [], '', false);
        $entityFactoryMock = $this->getMock('\Magento\GiftRegistry\Model\EntityFactory', [], [], '', false);
        $itemFactoryMock = $this->getMock('\Magento\GiftRegistry\Model\ItemFactory', [], [], '', false);
        $optionFactoryMock = $this->getMock('\Magento\GiftRegistry\Model\Item\OptionFactory', [], [], '', false);

        $this->model = new \Magento\GiftRegistry\Model\Observer(
            $this->helperMock, $designMock, $sessionMock, $entityFactoryMock, $itemFactoryMock, $optionFactoryMock
        );
    }

    /**
     * @covers \Magento\GiftRegistry\Model\Observer::addressDataBeforeSave
     *
     * @dataProvider addressDataBeforeSaveDataProvider
     * @param string $addressId
     * @param int $expectedCalls
     * @param int $expectedResult
     */
    public function testAddressDataBeforeSave($addressId, $expectedCalls, $expectedResult)
    {
        $addressMockMethods = ['getCustomerAddressId', 'setGiftregistryItemId', '__wakeup'];
        $addressMock = $this->getMock('\Magento\Sales\Model\Quote\Address', $addressMockMethods, [], '', false);
        $addressMock->expects($this->once())->method('getCustomerAddressId')->will($this->returnValue($addressId));
        $addressMock->expects($this->exactly($expectedCalls))->method('setGiftregistryItemId')->with($expectedResult);

        $event = new \Magento\Framework\Object();
        $event->setDataObject($addressMock);

        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $this->helperMock->expects($this->any())->method('getAddressIdPrefix')->will($this->returnValue('gr_address_'));

        $this->model->addressDataBeforeSave($observerMock);
    }

    /**
     * @return array
     */
    public function addressDataBeforeSaveDataProvider()
    {
        return [
            [
                'addressId' => 'gr_address_2',
                'expectedCalls' => 1,
                'expectedResult' => 2
            ],
            [
                'addressId' => 'gr_address_',
                'expectedCalls' => 0,
                'expectedResult' => ''
            ],
            [
                'addressId' => '2',
                'expectedCalls' => 0,
                'expectedResult' => ''
            ],
        ];
    }
}
