<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Address;

use \Magento\Checkout\Service\V1\Data\Cart\Address;
use \Magento\Checkout\Service\V1\Data\Cart\Address\Region;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Converter
     */
    protected $model;

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
        $this->addressBuilderMock = $this->getMock(
            '\Magento\Checkout\Service\V1\Data\Cart\AddressBuilder', [], [], '', false
        );

        $this->model = new Converter($this->addressBuilderMock);
    }

    /**
     * @covers \Magento\Checkout\Service\V1\Address\Converter::convert
     */
    public  function testConvert()
    {
        $addressMockMethods = [
            'getCountryId', 'getId', 'getCustomerId', 'getRegion', 'getRegionId', 'getRegionCode',
            'getStreet', 'getCompany', 'getTelephone', 'getFax', 'getPostcode', 'getFirstname', 'getMiddlename',
            'getLastname', 'getPrefix', 'getSuffix', 'getEmail', 'getVatId', '__wakeup'
        ];
        $addressMock = $this->getMock('\Magento\Sales\Model\Quote\Address', $addressMockMethods, [], '', false);

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
        $addressMock->expects($this->any())->method('getEmail')->will($this->returnValue('aaa@aaa.com'));
        $addressMock->expects($this->any())->method('getVatId')->will($this->returnValue(5));

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
            Address::KEY_EMAIL => 'aaa@aaa.com',
            Address::KEY_VAT_ID => 5
        ];

        $this->addressBuilderMock->expects($this->once())->method('populateWithArray')->with($testData)
            ->will($this->returnValue($this->addressBuilderMock));
        $this->addressBuilderMock->expects($this->once())->method('create')->will($this->returnValue('Expected value'));

        $this->assertEquals('Expected value', $this->model->convert($addressMock));
    }
}
