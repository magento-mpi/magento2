<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getReturnAddressDataProvider
     */
    public function testGetReturnAddressData($useStoreAddress, $scopeConfigData, $mockConfig, $expectedResult)
    {
        $scopeConfigMock = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $scopeConfigMock->expects(
            $this->atLeastOnce()
        )->method(
            'isSetFlag'
        )->with(
            \Magento\Rma\Model\Rma::XML_PATH_USE_STORE_ADDRESS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $mockConfig['store_id']
        )->will(
            $this->returnValue($useStoreAddress)
        );

        $scopeConfigMock->expects(
            $this->atLeastOnce()
        )->method(
            'getValue'
        )->will(
            $this->returnValueMap($scopeConfigData)
        );

        $context = $this->getMock('Magento\Framework\App\Helper\Context', ['getApp'], [], '', false, false);

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var \Magento\Rma\Helper\Data $model */
        $model = $objectManager->getObject(
            'Magento\Rma\Helper\Data',
            [
                'context' => $context,
                'scopeConfig' => $scopeConfigMock,
                'countryFactory' => $this->_getCountryFactoryMock($mockConfig),
                'regionFactory' => $this->_getRegionFactoryMock($mockConfig),
                'itemFactory' => $this->getMock('Magento\Rma\Model\Resource\ItemFactory', [], [], '', false),
                'addressFactory' => $this->getMock(
                    'Magento\Sales\Model\Quote\AddressFactory',
                    [],
                    [],
                    '',
                    false
                )
            ]
        );
        $this->assertEquals($model->getReturnAddressData($mockConfig['store_id']), $expectedResult);
    }

    /**
     * Create country factory mock
     *
     * @param array $mockConfig
     * @return \Magento\Directory\Model\Country|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getCountryFactoryMock(array $mockConfig)
    {
        $countryMock = $this->getMock('Magento\Directory\Model\Country', [], [], '', false);
        $countryMock->expects($this->any())->method('loadByCode')->will($this->returnValue($countryMock));
        $countryMock->expects($this->any())->method('getName')->will($this->returnValue($mockConfig['country_name']));
        $countryFactoryMock = $this->getMock(
            'Magento\Directory\Model\CountryFactory',
            ['create'],
            [],
            '',
            false
        );
        $countryFactoryMock->expects($this->any())->method('create')->will($this->returnValue($countryMock));

        return $countryFactoryMock;
    }

    /**
     * Create region factory mock
     *
     * @param array $mockConfig
     * @return \Magento\Directory\Model\Region|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getRegionFactoryMock(array $mockConfig)
    {
        $regionMock = $this->getMock(
            'Magento\Directory\Model\Region',
            ['load', 'getCode', 'getName', '__wakeup'],
            [],
            '',
            false
        );
        $regionMock->expects($this->any())->method('load')->will($this->returnValue($regionMock));
        $regionMock->expects($this->any())->method('getCode')->will($this->returnValue($mockConfig['region_id']));
        $regionMock->expects($this->any())->method('getName')->will($this->returnValue($mockConfig['region_name']));
        $regionFactoryMock = $this->getMock('Magento\Directory\Model\RegionFactory', [], [], '', false);
        $regionFactoryMock->expects($this->any())->method('create')->will($this->returnValue($regionMock));

        return $regionFactoryMock;
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getReturnAddressDataProvider()
    {
        return [
            [
                true,
                [
                    [
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_CITY,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Kabul',
                    ],
                    [
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_COUNTRY_ID,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'AF'
                    ],
                    [
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ZIP,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        '912232'
                    ],
                    [
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_REGION_ID,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Kabul'
                    ],
                    [
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ADDRESS2,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Test Street 2'
                    ],
                    [
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ADDRESS1,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Test Street 1'
                    ]
                ],
                [
                    'store_id' => 1,
                    'country_name' => 'Afghanistan',
                    'region_name' => 'Kabul',
                    'region_id' => 'Kabul'
                ],
                [
                    'city' => 'Kabul',
                    'countryId' => 'AF',
                    'postcode' => '912232',
                    'region_id' => 'Kabul',
                    'street2' => 'Test Street 2',
                    'street1' => 'Test Street 1',
                    'country' => 'Afghanistan',
                    'region' => 'Kabul',
                    'company' => null,
                    'telephone' => null
                ],
            ],
            [
                false,
                [
                    [
                        \Magento\Rma\Model\Shipping::XML_PATH_CITY,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Kabul',
                    ],
                    [
                        \Magento\Rma\Model\Shipping::XML_PATH_COUNTRY_ID,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'AF'
                    ],
                    [
                        \Magento\Rma\Model\Shipping::XML_PATH_ZIP,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        '912232'
                    ],
                    [
                        \Magento\Rma\Model\Shipping::XML_PATH_REGION_ID,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Kabul'
                    ],
                    [
                        \Magento\Rma\Model\Shipping::XML_PATH_ADDRESS2,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Test Street 2'
                    ],
                    [
                        \Magento\Rma\Model\Shipping::XML_PATH_ADDRESS1,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Test Street 1'
                    ],
                    [
                        \Magento\Rma\Model\Shipping::XML_PATH_CONTACT_NAME,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Hafizullah Amin'
                    ]
                ],
                [
                    'store_id' => 1,
                    'country_name' => 'Afghanistan',
                    'region_name' => 'Kabul',
                    'region_id' => 'Kabul'
                ],
                [
                    'city' => 'Kabul',
                    'countryId' => 'AF',
                    'postcode' => '912232',
                    'region_id' => 'Kabul',
                    'street2' => 'Test Street 2',
                    'street1' => 'Test Street 1',
                    'country' => 'Afghanistan',
                    'firstname' => 'Hafizullah Amin',
                    'region' => 'Kabul',
                    'company' => null,
                    'telephone' => null
                ]
            ],
            [
                true,
                [
                    [
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_CITY,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Kabul',
                    ],
                    [
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_COUNTRY_ID,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        null
                    ],
                    [
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ZIP,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        '912232'
                    ],
                    [
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_REGION_ID,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Kabul'
                    ],
                    [
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ADDRESS2,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Test Street 2'
                    ],
                    [
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ADDRESS1,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Test Street 1'
                    ]
                ],
                [
                    'store_id' => 1,
                    'country_name' => 'Afghanistan',
                    'region_name' => 'Kabul',
                    'region_id' => 'Kabul'
                ],
                [
                    'city' => 'Kabul',
                    'countryId' => null,
                    'postcode' => '912232',
                    'region_id' => 'Kabul',
                    'street2' => 'Test Street 2',
                    'street1' => 'Test Street 1',
                    'country' => '',
                    'region' => 'Kabul',
                    'company' => null,
                    'telephone' => null
                ]
            ]
        ];
    }
}
