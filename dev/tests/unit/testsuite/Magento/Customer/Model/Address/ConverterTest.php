<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Address;

use Magento\Framework\Api\AttributeValue;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Converter
     */
    protected $model;

    /**
     * @var \Magento\Customer\Service\V1\Data\AddressBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressBuilderMock;

    /**
     * @var \Magento\Customer\Model\AddressFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressFactoryMock;

    /**
     * @var \Magento\Customer\Service\V1\Data\RegionBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $regionBuilderMock;

    /**
     *
     * @var \Magento\Customer\Service\V1\AddressMetadataServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressMetadataServiceMock;

    /**
     * @var \Magento\Customer\Model\Address\Mapper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressMapperMock;

    protected function setUp()
    {
        $this->addressBuilderMock = $this->getMock(
            'Magento\Customer\Service\V1\Data\AddressBuilder',
            ['populateWithArray', 'setId', 'setCustomerId', 'create'],
            [],
            '',
            false
        );

        $this->addressFactoryMock = $this->getMock(
            'Magento\Customer\Model\AddressFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->regionBuilderMock = $this->getMock(
            'Magento\Customer\Service\V1\Data\RegionBuilder',
            [],
            [],
            '',
            false
        );

        $this->addressMetadataServiceMock = $this->getMock(
            'Magento\Customer\Service\V1\AddressMetadataService',
            ['getAllAttributesMetadata'],
            [],
            '',
            false
        );

        $this->addressMapperMock = $this->getMock(
            'Magento\Customer\Model\Address\Mapper',
            ['toFlatArray'],
            [],
            '',
            false
        );

        $this->model = new Converter(
            $this->addressBuilderMock,
            $this->addressFactoryMock,
            $this->addressMetadataServiceMock,
            $this->addressMapperMock
        );
    }

    public function testUpdateAddressModel()
    {
        $this->addressMapperMock->expects($this->once())
            ->method('toFlatArray')
            ->will($this->returnValue([]));
        $addressModelMock = $this->getAddressModelMock();
        $addressModelMock->expects($this->once())
            ->method('getAttributeSetId')
            ->will($this->returnValue(false));
        $addressModelMock->expects($this->once())
            ->method('setAttributeSetId')
            ->with($this->equalTo(
                \Magento\Customer\Api\AddressMetadataInterface::ATTRIBUTE_SET_ID_ADDRESS
            ));

        $addressMock = $this->getMock('Magento\Customer\Service\V1\Data\Address', [], [], '', false);
        $this->model->updateAddressModel($addressModelMock, $addressMock);
    }

    public function testUpdateAddressModelWithAttributes()
    {
        $addressModelMock = $this->getAddressModelMock();
        $addressModelMock->expects($this->once())
            ->method('getAttributeSetId')
            ->will($this->returnValue(true));
        $addressModelMock->expects($this->never())
            ->method('setAttributeSetId');

        $attributes = [
            'custom_attributes' => [
                [AttributeValue::ATTRIBUTE_CODE => 'code_01', AttributeValue::VALUE => 'value_01'],
                [AttributeValue::ATTRIBUTE_CODE => 'code_02', AttributeValue::VALUE => 'value_02'],
                [AttributeValue::ATTRIBUTE_CODE => 'code_03', AttributeValue::VALUE => 'value_03'],
            ],
            'attributes_01' => ['some_value_01', 'some_value_02', 'some_value_03'],
            'attributes_02' => 'some_value_04',
            \Magento\Customer\Service\V1\Data\Address::KEY_REGION => 'some_region',
        ];
        $regionMock = $this->getMock(
            'Magento\Customer\Service\V1\Data\Region',
            ['getRegion', 'getRegionCode', 'getRegionId'],
            [],
            '',
            false
        );
        $regionMock->expects($this->once())->method('getRegion');
        $regionMock->expects($this->once())->method('getRegionCode');
        $regionMock->expects($this->once())->method('getRegionId');
        $addressMock = $this->getMock('Magento\Customer\Service\V1\Data\Address', [], [], '', false);
        $addressMock->expects($this->exactly(4))
            ->method('getRegion')
            ->will($this->returnValue($regionMock));

        $this->addressMapperMock->expects($this->once())
            ->method('toFlatArray')
            ->will($this->returnValue($attributes));

        $this->model->updateAddressModel($addressModelMock, $addressMock);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAddressModelMock()
    {
        $addressModelMock = $this->getMock(
            'Magento\Customer\Model\Address',
            [
                'setIsDefaultBilling',
                'setIsDefaultShipping',
                'setAttributeSetId',
                'getAttributeSetId',
                '__wakeup',
                'getCustomAttributesCodes'
            ],
            [],
            '',
            false
        );
        $addressModelMock->expects($this->once())->method('setIsDefaultBilling');
        $addressModelMock->expects($this->once())->method('setIsDefaultShipping');
        $addressModelMock->expects($this->any())->method('getCustomAttributesCodes')->willReturn([]);
        return $addressModelMock;
    }

    public function testCreateAddressFromModel()
    {
        $defaultBillingId = 1;
        $defaultShippingId = 1;
        $addressId = 1;

        $addressModelMock = $this->getAddressMockForCreate();
        $addressModelMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($addressId));
        $addressModelMock->expects($this->any())
            ->method('getCustomerId');
        $addressModelMock->expects($this->any())
            ->method('getParentId');

        $addressMock = $this->getMock('Magento\Customer\Service\V1\Data\Address', [], [], '', false);
        $this->addressMetadataServiceMock->expects($this->once())
            ->method('getAllAttributesMetadata')
            ->will($this->returnValue([]));
        $this->addressBuilderMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($addressMock));
        $this->addressBuilderMock->expects($this->once())
            ->method('setId')
            ->with($this->equalTo($addressId));
        $this->addressBuilderMock->expects($this->never())
            ->method('setCustomerId');
        $this->assertEquals(
            $addressMock,
            $this->model->createAddressFromModel($addressModelMock, $defaultBillingId, $defaultShippingId)
        );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testCreateAddressFromModelWithCustomerId()
    {
        $defaultBillingId = 1;
        $defaultShippingId = 1;
        $customerId = 1;
        $attributeCode = 'attribute_code';

        $addressModelMock = $this->getAddressMockForCreate();
        $addressModelMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(null));
        $addressModelMock->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue($customerId));
        $addressModelMock->expects($this->any())
            ->method('getParentId');
        $getData = function ($key, $index = null) use ($attributeCode, $customerId) {
            $result = null;
            switch ($key) {
                case $attributeCode:
                    $result = 'some_data';
                    break;
                case 'customer_id':
                    $result = $customerId;
                    break;
            }
            return $result;
        };
        $addressModelMock->expects($this->any())
            ->method('getData')
            ->will($this->returnCallback($getData));
        $attributeMock = $this->getMock(
            'Magento\Customer\Service\V1\Data\Eav\AttributeMetadata',
            ['getAttributeCode'],
            [],
            '',
            false
        );
        $attributeMock->expects($this->once())
            ->method('getAttributeCode')
            ->will($this->returnValue($attributeCode));

        $addressMock = $this->getMock('Magento\Customer\Service\V1\Data\Address', [], [], '', false);
        $this->addressMetadataServiceMock->expects($this->once())
            ->method('getAllAttributesMetadata')
            ->will($this->returnValue([$attributeMock]));
        $this->addressBuilderMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($addressMock));
        $this->addressBuilderMock->expects($this->once())
            ->method('setCustomerId')
            ->with($this->equalTo($customerId));
        $this->assertEquals(
            $addressMock,
            $this->model->createAddressFromModel($addressModelMock, $defaultBillingId, $defaultShippingId)
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAddressMockForCreate()
    {
        $addressModelMock = $this->getMockForAbstractClass(
            'Magento\Customer\Model\Address\AbstractAddress',
            [],
            '',
            false,
            false,
            false,
            [
                'getId',
                'getStreet',
                'getRegion',
                'getRegionId',
                'getRegionCode',
                'getCustomerId',
                'getParentId',
                'getData',
                '__wakeup',
            ]
        );
        return $addressModelMock;
    }
}
