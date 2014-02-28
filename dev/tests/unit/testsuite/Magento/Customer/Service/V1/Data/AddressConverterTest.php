<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data;

use Magento\Customer\Service\V1\CustomerMetadataService;

class AddressConverterTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\TestFramework\Helper\ObjectManager */
    protected $_objectManager;

    /** @var CustomerMetadataService */
    protected $_customerMetadataService;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var CustomerMetadataService $customerMetadataService */
        $this->_customerMetadataService = $this->getMockBuilder('Magento\Customer\Service\V1\CustomerMetadataService')
            ->setMethods(['getCustomAddressAttributeMetadata'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->_customerMetadataService->expects($this->any())
            ->method('getCustomAddressAttributeMetadata')
            ->will(
                $this->returnValue(
                    [
                        new \Magento\Object(['attribute_code' => 'warehouse_zip']),
                        new \Magento\Object(['attribute_code' => 'warehouse_alternate'])
                    ]
                )
            );
    }

    public function testToFlatArray()
    {
        $expected = [
            'id' => 1,
            'default_shipping' => false,
            'default_billing' => true,
            'firstname' => 'John',
            'lastname' => 'Doe',
            'street' => ['7700 W Parmer Ln'],
            'city' => 'Austin',
            'country_id' => 'US',
            'region_id' => 1,
            'region' => 'Texas',
            'region_code' => 'TX'
        ];

        $addressData = $this->_sampleAddressDataObject();
        $result = AddressConverter::toFlatArray($addressData);

        $this->assertEquals($expected, $result);
    }


    public function testToFlatArrayCustomAttributes()
    {
        $updatedAddressData = [
            'email' => 'test@example.com',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'unknown_key' => 'Golden Necklace',
            'warehouse_zip' => '78777',
            'warehouse_alternate' => '90051'
        ];

        $expected = [
            'id' => 1,
            'default_shipping' => false,
            'default_billing' => true,
            'firstname' => 'John',
            'lastname' => 'Doe',
            'street' => ['7700 W Parmer Ln'],
            'city' => 'Austin',
            'country_id' => 'US',
            'region_id' => 1,
            'region' => 'Texas',
            'region_code' => 'TX',
            'warehouse_zip' => '78777',
            'warehouse_alternate' => '90051'
        ];

        $addressData = $this->_sampleAddressDataObject();
        $addressData = (new AddressBuilder(new RegionBuilder(), $this->_customerMetadataService))
            ->mergeDataObjectWithArray($addressData, $updatedAddressData
        );

        $result = AddressConverter::toFlatArray($addressData);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return Address
     */
    protected function _sampleAddressDataObject()
    {
        $regionData = (new RegionBuilder())->setRegion('Texas')->setRegionId(1)->setRegionCode('TX');
        $addressData = (new AddressBuilder($regionData, $this->_customerMetadataService))
            ->setId('1')
            ->setDefaultBilling(true)
            ->setDefaultShipping(false)
            ->setCity('Austin')
            ->setFirstname('John')
            ->setLastname('Doe')
            ->setCountryId('US')
            ->setStreet(['7700 W Parmer Ln']);

        return $addressData->create();
    }
}
 