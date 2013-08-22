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
class Magento_Rma_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getReturnAddressDataProvider
     */
    public function testGetReturnAddressData($useStoreAddress, $storeConfigData, $mockConfig, $expectedResult)
    {
        $storeConfigMock = $this->getMock('Magento_Core_Model_Store_Config', array(), array(), '', false);
        $storeConfigMock->expects($this->any())
            ->method('getConfigFlag')
            ->with(Magento_Rma_Model_Rma::XML_PATH_USE_STORE_ADDRESS, $mockConfig['store_id'])
            ->will($this->returnValue($useStoreAddress));

        $storeConfigMock->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValueMap($storeConfigData));

        $model = new Magento_Rma_Helper_Data(
            $this->getMock('Magento_Core_Helper_Context', array(), array(), '', false, false),
            $this->_getAppMock($mockConfig),
            $storeConfigMock,
            $this->_getCountryFactoryMock($mockConfig),
            $this->_getRegionFactoryMock($mockConfig)

        );
        $this->assertEquals($model->getReturnAddressData(), $expectedResult);
    }

    /**
     * Create application mock
     *
     * @param array $mockConfig
     * @return Magento_Core_Model_App|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getAppMock($mockConfig)
    {
        $appMock = $this->getMock('Magento_Core_Model_App', array(), array(), '', false);
        $appMock->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($mockConfig['store_id']));
        return $appMock;
    }

    /**
     * Create country factory mock
     *
     * @param array $mockConfig
     * @return Magento_Directory_Model_Country|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getCountryFactoryMock(array $mockConfig)
    {
        $countryMock = $this->getMock('Magento_Directory_Model_Country', array(), array(), '', false);
        $countryMock->expects($this->any())
            ->method('loadByCode')
            ->will($this->returnValue($countryMock));
        $countryMock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($mockConfig['country_name']));
        $countryFactoryMock = $this->getMock(
            'Magento_Directory_Model_CountryFactory', array('create'), array(), '', false
        );
        $countryFactoryMock->expects($this->any())->method('create')->will($this->returnValue($countryMock));

        return $countryFactoryMock;
    }

    /**
     * Create region factory mock
     *
     * @param array $mockConfig
     * @return Magento_Directory_Model_Region|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getRegionFactoryMock(array $mockConfig)
    {
        $regionMock = $this->getMock(
            'Magento_Directory_Model_Region',
            array('load', 'getCode', 'getName'),
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
        $regionFactoryMock = $this->getMock('Magento_Directory_Model_RegionFactory', array(), array(), '', false);
        $regionFactoryMock->expects($this->any())->method('create')->will($this->returnValue($regionMock));

        return $regionFactoryMock;
    }

    public function getReturnAddressDataProvider()
    {
        return array(
            array(
                true,
                array(
                    array(Magento_Shipping_Model_Shipping::XML_PATH_STORE_CITY, 1, 'Kabul'),
                    array(Magento_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID, 1, 'AF'),
                    array(Magento_Shipping_Model_Shipping::XML_PATH_STORE_ZIP, 1, '912232'),
                    array(Magento_Shipping_Model_Shipping::XML_PATH_STORE_REGION_ID, 1, 'Kabul'),
                    array(Magento_Shipping_Model_Shipping::XML_PATH_STORE_ADDRESS2, 1, 'Test Street 2'),
                    array(Magento_Shipping_Model_Shipping::XML_PATH_STORE_ADDRESS1, 1, 'Test Street 1'),
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
                    array(Magento_Rma_Model_Shipping::XML_PATH_CITY, 1, 'Kabul'),
                    array(Magento_Rma_Model_Shipping::XML_PATH_COUNTRY_ID, 1, 'AF'),
                    array(Magento_Rma_Model_Shipping::XML_PATH_ZIP, 1, '912232'),
                    array(Magento_Rma_Model_Shipping::XML_PATH_REGION_ID, 1, 'Kabul'),
                    array(Magento_Rma_Model_Shipping::XML_PATH_ADDRESS2, 1, 'Test Street 2'),
                    array(Magento_Rma_Model_Shipping::XML_PATH_ADDRESS1, 1, 'Test Street 1'),
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
                    array(Magento_Shipping_Model_Shipping::XML_PATH_STORE_CITY, 1, 'Kabul'),
                    array(Magento_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID, 1, null),
                    array(Magento_Shipping_Model_Shipping::XML_PATH_STORE_ZIP, 1, '912232'),
                    array(Magento_Shipping_Model_Shipping::XML_PATH_STORE_REGION_ID, 1, 'Kabul'),
                    array(Magento_Shipping_Model_Shipping::XML_PATH_STORE_ADDRESS2, 1, 'Test Street 2'),
                    array(Magento_Shipping_Model_Shipping::XML_PATH_STORE_ADDRESS1, 1, 'Test Street 1'),
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
