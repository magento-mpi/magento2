<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Address\Billing;

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
    protected $addressFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteAddressMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->quoteLoaderMock = $this->getMock('\Magento\Checkout\Service\V1\QuoteLoader', [], [], '', false);
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $this->addressFactoryMock = $this->getMock(
            '\Magento\Sales\Model\Quote\AddressFactory', ['create', '__wakeup'], [], '', false
        );

        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->quoteAddressMock = $this->getMock(
            '\Magento\Sales\Model\Quote\Address',
            ['getCustomerId', 'load', 'getData', 'setData', 'setStreet', 'setRegionId', 'setRegion', '__wakeup'],
            [],
            '',
            false
        );
        $this->addressFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->quoteAddressMock));
        $this->service = $this->objectManager->getObject(
            '\Magento\Checkout\Service\V1\Address\Billing\WriteService',
            [
                'quoteLoader' => $this->quoteLoaderMock,
                'storeManager' => $this->storeManagerMock,
                'quoteAddressFactory' => $this->addressFactoryMock,
            ]
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Invalid address id 300
     */
    public  function testSetAddressInvalidId()
    {
        $storeId = 323;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', [], [], '', false);
        $this->quoteLoaderMock->expects($this->once())
            ->method('load')
            ->with('cartId', $storeId)
            ->will($this->returnValue($quoteMock));

        /** @var \Magento\Checkout\Service\V1\Data\Cart\AddressBuilder $addressDataBuilder */
        $addressDataBuilder = $this->objectManager->getObject('Magento\Checkout\Service\V1\Data\Cart\AddressBuilder');
        $addressId = 300;
        /** @var \Magento\Checkout\Service\V1\Data\Cart\Address $addressData */
        $addressData = $addressDataBuilder->setId($addressId)->create();
        $this->quoteAddressMock->expects($this->once())->method('load')->with($addressId);
        $this->quoteAddressMock->expects($this->once())->method('getData')->will($this->returnValue([]));

        $this->service->setAddress('cartId', $addressData);
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage Address with id 300 belongs to another customer
     */
    public  function testSetAddressAnotherCustomer()
    {
        $storeId = 323;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', [], [], '', false);
        $this->quoteLoaderMock->expects($this->once())
            ->method('load')
            ->with('cartId', $storeId)
            ->will($this->returnValue($quoteMock));

        /** @var \Magento\Checkout\Service\V1\Data\Cart\AddressBuilder $addressDataBuilder */
        $addressDataBuilder = $this->objectManager->getObject('Magento\Checkout\Service\V1\Data\Cart\AddressBuilder');
        $addressId = 300;
        /** @var \Magento\Checkout\Service\V1\Data\Cart\Address $addressData */
        $addressData = $addressDataBuilder->setId($addressId)->setCustomerId(3)->create();
        $this->quoteAddressMock->expects($this->once())->method('load')->with($addressId);
        $this->quoteAddressMock->expects($this->once())->method('getData')->will($this->returnValue([1]));
        $this->quoteAddressMock->expects($this->once())->method('getCustomerId')->will($this->returnValue(2));

        $this->service->setAddress('cartId', $addressData);
    }

    public  function testSetAddress()
    {
        $storeId = 323;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', [], [], '', false);
        $this->quoteLoaderMock->expects($this->once())
            ->method('load')
            ->with('cartId', $storeId)
            ->will($this->returnValue($quoteMock));

        /** @var \Magento\Checkout\Service\V1\Data\Cart\AddressBuilder $addressDataBuilder */
        $addressDataBuilder = $this->objectManager->getObject('Magento\Checkout\Service\V1\Data\Cart\AddressBuilder');

        $regionMock = $this->getMock(
            '\Magento\Checkout\Service\V1\Data\Cart\Address\Region', [], [], '', false
        );
        $street = 'Sample Street';
        $regionId = 23;
        $regionName = 'California';

        $regionMock->expects($this->once())->method('getRegionId')->will($this->returnValue($regionId));
        $regionMock->expects($this->once())->method('getRegion')->will($this->returnValue($regionName));

        /** @var \Magento\Checkout\Service\V1\Data\Cart\Address $addressData */
        $addressData = $addressDataBuilder
            ->setStreet($street)
            ->setRegion($regionMock)
            ->create();
        $this->quoteAddressMock->expects($this->once())->method('setData');
        $this->quoteAddressMock->expects($this->once())->method('setStreet')->with($street);
        $this->quoteAddressMock->expects($this->once())->method('setRegionId')->with($regionId);
        $this->quoteAddressMock->expects($this->once())->method('setRegion')->with($regionName);

        $quoteMock->expects($this->once())->method('setBillingAddress')->with($this->quoteAddressMock);
        $quoteMock->expects($this->once())->method('setDataChanges')->with(true);
        $quoteMock->expects($this->once())->method('save');

        $this->service->setAddress('cartId', $addressData);
    }
}
