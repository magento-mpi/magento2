<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Address\Shipping;

use \Magento\Checkout\Service\V1\Data\Cart\Address;
use \Magento\Customer\Service\V1\Data\Region;

class ReaderServiceTest extends \PHPUnit_Framework_TestCase
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
    protected $addressBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    protected function setUp()
    {
        $this->quoteLoaderMock = $this->getMock('\Magento\Checkout\Service\V1\QuoteLoader', [], [], '', false);
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $this->addressBuilderMock = $this->getMock(
            '\Magento\Checkout\Service\V1\Data\Cart\AddressBuilder', [], [], '', false
        );

        $this->service = new ReadService($this->quoteLoaderMock, $this->addressBuilderMock, $this->storeManagerMock);
    }

    /**
     * @covers \Magento\Checkout\Service\V1\Address\Shipping\ReadService::getAddress
     */
    public  function testGetAddress()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue(11));

        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', [], [], '', false);
        $this->quoteLoaderMock->expects($this->once())->method('load')->with('cartId', '11')
            ->will($this->returnValue($quoteMock));

        $addressMockMethods = [
            'getCountryId', 'getId', 'getCustomerId', 'getRegion', 'getRegionId', 'getRegionCode',
            'getStreet', 'getCompany', 'getTelephone', 'getFax', 'getPostcode', 'getFirstname', 'getMiddlename',
            'getLastname', 'getPrefix', 'getSuffix', '__wakeup'
        ];
        $addressMock = $this->getMock('\Magento\Sales\Model\Quote\Item', $addressMockMethods, [], '', false);
        $quoteMock->expects($this->any())->method('getShippingAddress')->will($this->returnValue($addressMock));

        $addressMock->expects($this->any())->method('getCountryId')->will($this->returnValue(1));
        $addressMock->expects($this->any())->method('getId')->will($this->returnValue(2));
        $addressMock->expects($this->any())->method('getCustomerId')->will($this->returnValue(3));
        $addressMock->expects($this->any())->method('getRegion')->will($this->returnValue('Alabama'));
        $addressMock->expects($this->any())->method('getRegionId')->will($this->returnValue(4));
        $addressMock->expects($this->any())->method('getRegionCode')->will($this->returnValue('aa'));
        $addressMock->expects($this->any())->method('getStreet')->will($this->returnValue('street'));
        $addressMock->expects($this->any())->method('getCompany')->will($this->returnValue('company'));
        $addressMock->expects($this->any())->method('getTelephone')->will($this->returnValue('123-123'));
        $addressMock->expects($this->any())->method('getFax')->will($this->returnValue('234-234'));
        $addressMock->expects($this->any())->method('getPostcode')->will($this->returnValue('80010'));
        $addressMock->expects($this->any())->method('getFirstname')->will($this->returnValue('Vasya'));
        $addressMock->expects($this->any())->method('getMiddlename')->will($this->returnValue('Vasya'));
        $addressMock->expects($this->any())->method('getLastname')->will($this->returnValue('Pupkin'));
        $addressMock->expects($this->any())->method('getPrefix')->will($this->returnValue('prefix'));
        $addressMock->expects($this->any())->method('getSuffix')->will($this->returnValue('suffix'));

        $testData = [
            Address::KEY_COUNTRY_ID => 1,
            Address::KEY_ID => 2,
            Address::KEY_CUSTOMER_ID => 3,
            Address::KEY_REGION => [
                Region::KEY_REGION => 'Alabama',
                Region::KEY_REGION_ID => 4,
                Region::KEY_REGION_CODE => 'aa',
            ],
            Address::KEY_STREET => 'street',
            Address::KEY_COMPANY => 'company',
            Address::KEY_TELEPHONE => '123-123',
            Address::KEY_FAX => '234-234',
            Address::KEY_POSTCODE => '80010',
            Address::KEY_FIRSTNAME => 'Vasya',
            Address::KEY_LASTNAME => 'Pupkin',
            Address::KEY_MIDDLENAME => 'Vasya',
            Address::KEY_PREFIX => 'prefix',
            Address::KEY_SUFFIX => 'suffix',
        ];
        $this->addressBuilderMock->expects($this->once())->method('populateWithArray')->with($testData)
            ->will($this->returnValue($this->addressBuilderMock));
        $this->addressBuilderMock->expects($this->once())->method('create')->will($this->returnValue('Expected value'));

        $this->assertEquals('Expected value', $this->service->getAddress('cartId'));
    }
}
