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

    public function testPopulateWithArray()
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
            'test' => 'xxx',
            FakeAddressInterface::DEFAULT_BILLING => false,
            FakeAddressInterface::DEFAULT_SHIPPING => true,
        ];

        /** @var \Magento\Wonderland\Api\Data\FakeAddressInterface $address */
        $address = $addressBuilder->populateWithArray($data)
            ->create();
        $this->assertInstanceOf('\Magento\Wonderland\Api\Data\FakeAddressInterface', $address);
        $this->assertEquals('Johnes', $address->getLastname());
        $this->assertEquals(true, $address->isDefaultShipping());
        $this->assertEquals(false, $address->isDefaultBilling());
        $this->assertNull($address->getCustomAttribute('test'));
        $this->assertInstanceOf('\Magento\Wonderland\Api\Data\FakeRegionInterface', $address->getRegion());
        $this->assertInstanceOf('\Magento\Wonderland\Api\Data\FakeRegionInterface', $address->getRegions()[0]);
        $this->assertInstanceOf('\Magento\Wonderland\Api\Data\FakeRegionInterface', $address->getRegions()[1]);
    }
}
