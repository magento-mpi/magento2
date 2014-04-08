<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data;

use Magento\Customer\Service\V1\CustomerMetadataService;
use Magento\Service\Data\Eav\AttributeValueBuilder;

class AddressConverterTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\TestFramework\Helper\ObjectManager */
    protected $_objectManager;

    /** @var CustomerMetadataService */
    protected $_customerMetadataService;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var CustomerMetadataService $customerMetadataService */
        $this->_customerMetadataService = $this->getMockBuilder(
            'Magento\Customer\Service\V1\CustomerMetadataService'
        )->setMethods(
            array('getCustomAddressAttributeMetadata')
        )->disableOriginalConstructor()->getMock();
        $this->_customerMetadataService->expects(
            $this->any()
        )->method(
            'getCustomAddressAttributeMetadata'
        )->will(
            $this->returnValue(
                array(
                    new \Magento\Object(array('attribute_code' => 'warehouse_zip')),
                    new \Magento\Object(array('attribute_code' => 'warehouse_alternate'))
                )
            )
        );
    }

    public function testToFlatArray()
    {
        $expected = array(
            'id' => 1,
            'default_shipping' => false,
            'default_billing' => true,
            'firstname' => 'John',
            'lastname' => 'Doe',
            'street' => array('7700 W Parmer Ln'),
            'city' => 'Austin',
            'country_id' => 'US',
            'region_id' => 1,
            'region' => 'Texas',
            'region_code' => 'TX'
        );

        $addressData = $this->_sampleAddressDataObject();
        $result = AddressConverter::toFlatArray($addressData);

        $this->assertEquals($expected, $result);
    }

    public function testToFlatArrayCustomAttributes()
    {
        $updatedAddressData = array(
            'email' => 'test@example.com',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'unknown_key' => 'Golden Necklace',
            'warehouse_zip' => '78777',
            'warehouse_alternate' => '90051'
        );

        $expected = array(
            'id' => 1,
            'default_shipping' => false,
            'default_billing' => true,
            'firstname' => 'John',
            'lastname' => 'Doe',
            'street' => array('7700 W Parmer Ln'),
            'city' => 'Austin',
            'country_id' => 'US',
            'region_id' => 1,
            'region' => 'Texas',
            'region_code' => 'TX',
            'warehouse_zip' => '78777',
            'warehouse_alternate' => '90051'
        );

        $addressData = $this->_sampleAddressDataObject();
        $addressData = (new AddressBuilder(
            new AttributeValueBuilder(),
            new RegionBuilder(),
            $this->_customerMetadataService
        ))->mergeDataObjectWithArray(
            $addressData,
            $updatedAddressData
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
        $addressData = (new AddressBuilder(
            new AttributeValueBuilder(),
            $regionData,
            $this->_customerMetadataService
        ))->setId(
            '1'
        )->setDefaultBilling(
            true
        )->setDefaultShipping(
            false
        )->setCity(
            'Austin'
        )->setFirstname(
            'John'
        )->setLastname(
            'Doe'
        )->setCountryId(
            'US'
        )->setStreet(
            array('7700 W Parmer Ln')
        );

        return $addressData->create();
    }
}
