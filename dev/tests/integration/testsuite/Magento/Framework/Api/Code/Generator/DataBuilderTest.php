<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Api\Code\Generator;

use Magento\Wonderland\Api\Data\FakeAddressInterface;
use Magento\Wonderland\Api\Data\FakeRegionInterface;
use Magento\Wonderland\Model\Data\FakeAddress;
use Magento\Wonderland\Model\Data\FakeRegion;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Api\AttributeInterface;

class DataBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\ObjectManager */
    private $_objectManager;

    protected function setUp()
    {
        $includePath = new \Magento\Framework\Autoload\IncludePath();
        $includePath->addIncludePath([__DIR__ . '/../../_files']);
        spl_autoload_register([$includePath, 'load']);
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_objectManager->configure(
            [
                'preferences' => [
                    'Magento\Wonderland\Api\Data\FakeAddressInterface' => 'Magento\Wonderland\Model\FakeAddress',
                    'Magento\Wonderland\Api\Data\FakeRegionInterface' => 'Magento\Wonderland\Model\FakeRegion'
                ]
            ]
        );
    }

    public function testDataObjectBuilder()
    {
        $regionBuilder = $this->_objectManager->create('Magento\Wonderland\Model\Data\FakeRegionBuilder');
        $this->assertInstanceOf('\Magento\Wonderland\Model\Data\FakeRegionBuilder', $regionBuilder);
        $region = $regionBuilder->setRegion('test')
            ->setRegionCode('test_code')
            ->setRegionId('test_id')
            ->create();
        $this->assertInstanceOf('\Magento\Wonderland\Model\Data\FakeRegion', $region);
        $this->assertEquals('test', $region->getRegion());
    }

    public function testDataObjectPopulateWithArray()
    {
        /** @var \Magento\Wonderland\Model\Data\FakeAddressBuilder $addressBuilder */
        $addressBuilder = $this->_objectManager->create('Magento\Wonderland\Model\Data\FakeAddressBuilder');

        $data = [
            FakeAddress::ID => 1,
            FakeAddress::CITY => 'Kiev',
            FakeAddress::REGION => [
                FakeRegion::REGION => 'US',
                FakeRegion::REGION_CODE => 'TX',
                FakeRegion::REGION_ID => '1',
            ],
            FakeAddress::REGIONS => [
                [
                    FakeRegion::REGION => 'US',
                    FakeRegion::REGION_CODE => 'TX',
                    FakeRegion::REGION_ID => '1',
                ], [
                    FakeRegion::REGION => 'US',
                    FakeRegion::REGION_CODE => 'TX',
                    FakeRegion::REGION_ID => '2',
                ]
            ],
            ExtensibleDataInterface::CUSTOM_ATTRIBUTES => [
                [AttributeInterface::ATTRIBUTE_CODE => 'test', AttributeInterface::VALUE => 'test']
            ],
            FakeAddress::COMPANY => 'Magento',
            FakeAddress::COUNTRY_ID => 'US',
            FakeAddress::CUSTOMER_ID => '1',
            FakeAddress::FAX => '222',
            FakeAddress::FIRSTNAME => 'John',
            FakeAddress::MIDDLENAME => 'Dow',
            FakeAddress::LASTNAME => 'Johnes',
            FakeAddress::SUFFIX => 'Jr.',
            FakeAddress::POSTCODE => '78757',
            FakeAddress::PREFIX => 'Mr.',
            FakeAddress::STREET => 'Oak rd.',
            FakeAddress::TELEPHONE => '1234567',
            FakeAddress::VAT_ID => '1',
            'test' => 'xxx'
        ];

        /** @var \Magento\Wonderland\Api\Data\FakeAddressInterface $address */
        $address = $addressBuilder->populateWithArray($data)
            ->create();
        $this->assertInstanceOf('\Magento\Wonderland\Model\Data\FakeAddress', $address);
        $this->assertEquals('Johnes', $address->getLastname());
        $this->assertNull($address->getCustomAttribute('test'));
        $this->assertEmpty($address->getCustomAttributes());
        $this->assertInstanceOf('\Magento\Wonderland\Model\Data\FakeRegion', $address->getRegion());
        $this->assertInstanceOf('\Magento\Wonderland\Model\Data\FakeRegion', $address->getRegions()[0]);
        $this->assertInstanceOf('\Magento\Wonderland\Model\Data\FakeRegion', $address->getRegions()[1]);
    }

    public function testModelPopulateWithArray()
    {
        /** @var \Magento\Wonderland\Api\Data\FakeAddressDataBuilder $addressBuilder */
        $addressBuilder = $this->_objectManager->create('Magento\Wonderland\Api\Data\FakeAddressDataBuilder');

        $data = [
            FakeAddressInterface::ID => 1,
            FakeAddressInterface::CITY => 'Kiev',
            FakeAddressInterface::REGION => [
                FakeRegionInterface::REGION => 'US',
                FakeRegionInterface::REGION_CODE => 'TX',
                FakeRegionInterface::REGION_ID => '1',
            ],
            FakeAddressInterface::REGIONS => [
                [
                    FakeRegionInterface::REGION => 'US',
                    FakeRegionInterface::REGION_CODE => 'TX',
                    FakeRegionInterface::REGION_ID => '1',
                ], [
                    FakeRegionInterface::REGION => 'US',
                    FakeRegionInterface::REGION_CODE => 'TX',
                    FakeRegionInterface::REGION_ID => '2',
                ]
            ],
            FakeAddressInterface::COMPANY => 'Magento',
            FakeAddressInterface::COUNTRY_ID => 'US',
            FakeAddressInterface::CUSTOMER_ID => '1',
            FakeAddressInterface::FAX => '222',
            FakeAddressInterface::FIRSTNAME => 'John',
            FakeAddressInterface::MIDDLENAME => 'Dow',
            FakeAddressInterface::LASTNAME => 'Johnes',
            FakeAddressInterface::SUFFIX => 'Jr.',
            FakeAddressInterface::POSTCODE => '78757',
            FakeAddressInterface::PREFIX => 'Mr.',
            FakeAddressInterface::STREET => 'Oak rd.',
            FakeAddressInterface::TELEPHONE => '1234567',
            FakeAddressInterface::VAT_ID => '1',
            'test' => 'xxx'
        ];

        /** @var \Magento\Wonderland\Api\Data\FakeAddressInterface $address */
        $address = $addressBuilder->populateWithArray($data)
            ->create();
        $this->assertInstanceOf('\Magento\Wonderland\Api\Data\FakeAddressInterface', $address);
        $this->assertEquals('Johnes', $address->getLastname());
        $this->assertNull($address->getCustomAttribute('test'));
        $this->assertInstanceOf('\Magento\Wonderland\Api\Data\FakeRegionInterface', $address->getRegion());
        $this->assertInstanceOf('\Magento\Wonderland\Api\Data\FakeRegionInterface', $address->getRegions()[0]);
        $this->assertInstanceOf('\Magento\Wonderland\Api\Data\FakeRegionInterface', $address->getRegions()[1]);
    }

}