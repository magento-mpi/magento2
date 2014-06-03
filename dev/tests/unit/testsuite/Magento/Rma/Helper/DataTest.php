<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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

        $context = $this->getMock('Magento\Framework\App\Helper\Context', array('getApp'), array(), '', false, false);

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var \Magento\Rma\Helper\Data $model */
        $model = $objectManager->getObject(
            'Magento\Rma\Helper\Data',
            array(
                'context' => $context,
                'scopeConfig' => $scopeConfigMock,
                'countryFactory' => $this->_getCountryFactoryMock($mockConfig),
                'regionFactory' => $this->_getRegionFactoryMock($mockConfig),
                'itemFactory' => $this->getMock('Magento\Rma\Model\Resource\ItemFactory', array(), array(), '', false),
                'addressFactory' => $this->getMock(
                    'Magento\Sales\Model\Quote\AddressFactory',
                    array(),
                    array(),
                    '',
                    false
                )
            )
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
        $countryMock = $this->getMock('Magento\Directory\Model\Country', array(), array(), '', false);
        $countryMock->expects($this->any())->method('loadByCode')->will($this->returnValue($countryMock));
        $countryMock->expects($this->any())->method('getName')->will($this->returnValue($mockConfig['country_name']));
        $countryFactoryMock = $this->getMock(
            'Magento\Directory\Model\CountryFactory',
            array('create'),
            array(),
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
            array('load', 'getCode', 'getName', '__wakeup'),
            array(),
            '',
            false
        );
        $regionMock->expects($this->any())->method('load')->will($this->returnValue($regionMock));
        $regionMock->expects($this->any())->method('getCode')->will($this->returnValue($mockConfig['region_id']));
        $regionMock->expects($this->any())->method('getName')->will($this->returnValue($mockConfig['region_name']));
        $regionFactoryMock = $this->getMock('Magento\Directory\Model\RegionFactory', array(), array(), '', false);
        $regionFactoryMock->expects($this->any())->method('create')->will($this->returnValue($regionMock));

        return $regionFactoryMock;
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getReturnAddressDataProvider()
    {
        return array(
            array(
                true,
                array(
                    array(
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_CITY,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Kabul'
                    ),
                    array(
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_COUNTRY_ID,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'AF'
                    ),
                    array(
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ZIP,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        '912232'
                    ),
                    array(
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_REGION_ID,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Kabul'
                    ),
                    array(
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ADDRESS2,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Test Street 2'
                    ),
                    array(
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ADDRESS1,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Test Street 1'
                    )
                ),
                array(
                    'store_id' => 1,
                    'country_name' => 'Afghanistan',
                    'region_name' => 'Kabul',
                    'region_id' => 'Kabul'
                ),
                array(
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
                )
            ),
            array(
                false,
                array(
                    array(
                        \Magento\Rma\Model\Shipping::XML_PATH_CITY,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Kabul'
                    ),
                    array(
                        \Magento\Rma\Model\Shipping::XML_PATH_COUNTRY_ID,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'AF'
                    ),
                    array(
                        \Magento\Rma\Model\Shipping::XML_PATH_ZIP,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        '912232'
                    ),
                    array(
                        \Magento\Rma\Model\Shipping::XML_PATH_REGION_ID,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Kabul'
                    ),
                    array(
                        \Magento\Rma\Model\Shipping::XML_PATH_ADDRESS2,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Test Street 2'
                    ),
                    array(
                        \Magento\Rma\Model\Shipping::XML_PATH_ADDRESS1,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Test Street 1'
                    ),
                    array(
                        \Magento\Rma\Model\Shipping::XML_PATH_CONTACT_NAME,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Hafizullah Amin'
                    )
                ),
                array(
                    'store_id' => 1,
                    'country_name' => 'Afghanistan',
                    'region_name' => 'Kabul',
                    'region_id' => 'Kabul'
                ),
                array(
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
                )
            ),
            array(
                true,
                array(
                    array(
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_CITY,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Kabul'
                    ),
                    array(
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_COUNTRY_ID,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        null
                    ),
                    array(
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ZIP,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        '912232'
                    ),
                    array(
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_REGION_ID,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Kabul'
                    ),
                    array(
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ADDRESS2,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Test Street 2'
                    ),
                    array(
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ADDRESS1,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        1,
                        'Test Street 1'
                    )
                ),
                array(
                    'store_id' => 1,
                    'country_name' => 'Afghanistan',
                    'region_name' => 'Kabul',
                    'region_id' => 'Kabul'
                ),
                array(
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
                )
            )
        );
    }
}
