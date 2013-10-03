<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getReturnAddressDataProvider
     */
    public function testGetReturnAddressData($useStoreAddress, $storeConfigData, $mockConfig, $expectedResult)
    {
        $storeConfigMock = $this->getMock('Magento\Core\Model\Store\Config', array(), array(), '', false);
        $storeConfigMock->expects($this->any())
            ->method('getConfigFlag')
            ->with(\Magento\Rma\Model\Rma::XML_PATH_USE_STORE_ADDRESS, $mockConfig['store_id'])
            ->will($this->returnValue($useStoreAddress));

        $storeConfigMock->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValueMap($storeConfigData));

        $context = $this->getMock('Magento\Core\Helper\Context', array('getApp'), array(), '', false, false);
        $context->expects($this->any())->method('getApp')->will($this->returnValue($this->_getAppMock($mockConfig)));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var \Magento\Rma\Helper\Data $model */
        $model = $objectManager->getObject(
            'Magento\Rma\Helper\Data',
            array(
                'context'        => $context,
                'storeConfig'    => $storeConfigMock,
                'countryFactory' => $this->_getCountryFactoryMock($mockConfig),
                'regionFactory'  => $this->_getRegionFactoryMock($mockConfig),
                'itemFactory'    => $this->getMock('Magento\Rma\Model\Resource\ItemFactory', array(), array(), '',
                    false
                ),
                'addressFactory' => $this->getMock('Magento\Sales\Model\Quote\AddressFactory', array(), array(), '',
                    false
                ),
            )
        );
        $this->assertEquals($model->getReturnAddressData($mockConfig['store_id']), $expectedResult);
    }

    /**
     * Create application mock
     *
     * @param array $mockConfig
     * @return \Magento\Core\Model\App|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getAppMock($mockConfig)
    {
        $appMock = $this->getMock('Magento\Core\Model\App', array(), array(), '', false);
        $appMock->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($mockConfig['store_id']));
        return $appMock;
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
        $countryMock->expects($this->any())
            ->method('loadByCode')
            ->will($this->returnValue($countryMock));
        $countryMock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($mockConfig['country_name']));
        $countryFactoryMock = $this->getMock(
            'Magento\Directory\Model\CountryFactory', array('create'), array(), '', false
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
        $regionMock->expects($this->any())
            ->method('load')
            ->will($this->returnValue($regionMock));
        $regionMock->expects($this->any())
            ->method('getCode')
            ->will($this->returnValue($mockConfig['region_id']));
        $regionMock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($mockConfig['region_name']));
        $regionFactoryMock = $this->getMock('Magento\Directory\Model\RegionFactory', array(), array(), '', false);
        $regionFactoryMock->expects($this->any())->method('create')->will($this->returnValue($regionMock));

        return $regionFactoryMock;
    }

    public function getReturnAddressDataProvider()
    {
        return array(
            array(
                true,
                array(
                    array(\Magento\Shipping\Model\Shipping::XML_PATH_STORE_CITY, 1, 'Kabul'),
                    array(\Magento\Shipping\Model\Shipping::XML_PATH_STORE_COUNTRY_ID, 1, 'AF'),
                    array(\Magento\Shipping\Model\Shipping::XML_PATH_STORE_ZIP, 1, '912232'),
                    array(\Magento\Shipping\Model\Shipping::XML_PATH_STORE_REGION_ID, 1, 'Kabul'),
                    array(\Magento\Shipping\Model\Shipping::XML_PATH_STORE_ADDRESS2, 1, 'Test Street 2'),
                    array(\Magento\Shipping\Model\Shipping::XML_PATH_STORE_ADDRESS1, 1, 'Test Street 1'),
                ),
                array(
                    'store_id' => 1,
                    'country_name' => 'Afghanistan',
                    'region_name' => 'Kabul',
                    'region_id' => 'Kabul',
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
                    array(\Magento\Rma\Model\Shipping::XML_PATH_CITY, 1, 'Kabul'),
                    array(\Magento\Rma\Model\Shipping::XML_PATH_COUNTRY_ID, 1, 'AF'),
                    array(\Magento\Rma\Model\Shipping::XML_PATH_ZIP, 1, '912232'),
                    array(\Magento\Rma\Model\Shipping::XML_PATH_REGION_ID, 1, 'Kabul'),
                    array(\Magento\Rma\Model\Shipping::XML_PATH_ADDRESS2, 1, 'Test Street 2'),
                    array(\Magento\Rma\Model\Shipping::XML_PATH_ADDRESS1, 1, 'Test Street 1'),
                ),
                array(
                    'store_id' => 1,
                    'country_name' => 'Afghanistan',
                    'region_name' => 'Kabul',
                    'region_id' => 'Kabul',
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
            // Test Case which checks that country name is an empty string for wrong country_id
            array(
                true,
                array(
                    array(\Magento\Shipping\Model\Shipping::XML_PATH_STORE_CITY, 1, 'Kabul'),
                    array(\Magento\Shipping\Model\Shipping::XML_PATH_STORE_COUNTRY_ID, 1, null),
                    array(\Magento\Shipping\Model\Shipping::XML_PATH_STORE_ZIP, 1, '912232'),
                    array(\Magento\Shipping\Model\Shipping::XML_PATH_STORE_REGION_ID, 1, 'Kabul'),
                    array(\Magento\Shipping\Model\Shipping::XML_PATH_STORE_ADDRESS2, 1, 'Test Street 2'),
                    array(\Magento\Shipping\Model\Shipping::XML_PATH_STORE_ADDRESS1, 1, 'Test Street 1'),
                ),
                array(
                    'store_id' => 1,
                    'country_name' => 'Afghanistan',
                    'region_name' => 'Kabul',
                    'region_id' => 'Kabul',
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
            ),
        );
    }
}
