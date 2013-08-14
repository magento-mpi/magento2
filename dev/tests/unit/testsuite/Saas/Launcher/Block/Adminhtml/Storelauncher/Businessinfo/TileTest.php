<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Saas_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Tile
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_TileTest extends PHPUnit_Framework_TestCase
{
    /**
     * Data array, used in configCallback method
     *
     * @var array
     */
    protected $_data;

    /**
     * Store Config Mock object
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_config;

    /**
     * @var Saas_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Tile
     */
    protected $_tileBlock;

    public function setUp()
    {
        $this->_data = array(
            'name' => 'Magento',
            'street_line1' => 'Zoologichna',
            'street_line2' => '5 A',
            'city' => 'Kiev',
            'postcode' => '03344',
            'region_id' => 5,
            'country_id' => 'US',
            'email' => 'test@example.com',
            'name' => 'Store Name',
        );

        $this->_config = $this->getMock('Magento_Core_Model_Store_Config', array('getConfig'), array(), '', false);
        $this->_config->expects($this->any())
            ->method('getConfig')
            ->will($this->returnCallback(array($this, 'configCallback')));

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_tileBlock = $objectManagerHelper->getObject(
            'Saas_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Tile',
            array(
                'storeConfig' => $this->_config,
            )
        );
    }

    /**
     * @dataProvider testGetAddressDataProvider
     * @param array $expectedData
     * @param array $regions Array of Regions for Current Country
     */
    public function testGetAddress($expectedData, $regions)
    {
        $tileBlock = $this->_getBusinessInfoTileBlockForGetAddressTest($regions);

        $result = $tileBlock->getAddress();
        $this->assertEquals($expectedData, $result);
    }

    /**
     * @dataProvider testIsBusinessAddressConfiguredDataProvider
     * @param boolean $expectedData
     * @param array $regions Array of Regions for Current Country
     */
    public function testIsBusinessAddressConfigured($expectedData, $regions)
    {
        $tileBlock = $this->_getBusinessInfoTileBlockForGetAddressTest($regions);
        $this->assertEquals($expectedData, $tileBlock->isBusinessAddressConfigured());

    }

    /**
     * @dataProvider testIsBusinessAddressNotConfiguredDataProvider
     * @param boolean $inputData
     * @param array $regions Array of Regions for Current Country
     */
    public function testIsBusinessAddressNotConfigured($inputData, $regions)
    {
        $this->_data = $inputData;
        $tileBlock = $this->_getBusinessInfoTileBlockForGetAddressTest($regions);
        $this->assertFalse($tileBlock->isBusinessAddressConfigured());
    }

    /**
     * Build Mock object
     *
     * @param array $regions Array of Regions for Current Country
     * @return Saas_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Tile
     */
    protected function _getBusinessInfoTileBlockForGetAddressTest($regions)
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);

        $countryModel = $this->getMock('Magento_Directory_Model_Country',
            array('loadByCode', 'getName'), array(), '', false
        );

        $countryModel->expects($this->once())
            ->method('loadByCode')
            ->with($this->_data['country_id'])
            ->will($this->returnValue($countryModel));

        $countryModel->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('United States'));

        $regionModel = $this->getMock('Magento_Directory_Model_Region',
            array('load', 'getName', 'getCollection'), array(), '', false
        );

        $collectionMock = $this->getMock('Magento_Directory_Model_Resource_Region',
            array('addCountryFilter', 'toOptionArray'), array(), '', false
        );

        $collectionMock->expects($this->once())
            ->method('addCountryFilter')
            ->with($this->_data['country_id'])
            ->will($this->returnValue($collectionMock));

        $collectionMock->expects($this->once())
            ->method('toOptionArray')
            ->will($this->returnValue($regions));

        $regionModel->expects($this->once())
            ->method('getCollection')
            ->will($this->returnValue($collectionMock));

        if (!empty($regions)) {
            $regionModel->expects($this->once())
                ->method('load')
                ->with($this->_data['region_id'])
                ->will($this->returnValue($regionModel));

            $regionModel->expects($this->once())
                ->method('getName')
                ->will($this->returnValue('Alaska'));
        }

        $arguments = array(
            'storeConfig' => $this->_config,
            'countryModel' => $countryModel,
            'regionModel' => $regionModel,
        );

        $tileBlock = $objectManagerHelper->getObject(
            'Saas_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Tile',
            $arguments
        );

        return $tileBlock;
    }

    /**
     * @dataProvider testGetAddressWithoutCountryDataProvider
     * @param array $expectedData
     * @covers Saas_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Tile::getAddress
     */
    public function testGetAddressEmptyCountry(array $expectedData)
    {
        //Remove country dependent data
        unset($this->_data['country_id']);
        unset($this->_data['region_id']);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);

        $countryModel = $this->getMock('Magento_Directory_Model_Country', array(), array(), '', false);
        $regionModel = $this->getMock('Magento_Directory_Model_Region', array(), array(), '', false);

        $arguments = array(
            'storeConfig' => $this->_config,
            'countryModel' => $countryModel,
            'regionModel' => $regionModel,
        );

        $tileBlock = $objectManagerHelper->getObject(
            'Saas_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Tile',
            $arguments
        );

        $result = $tileBlock->getAddress();
        $this->assertEquals($expectedData, $result);
    }

    /**
     * Callback function for getConfig method
     *
     * @param string $param
     * @return string
     */
    public function configCallback($param)
    {
        $configPath = explode('/', $param);
        $configElement = $configPath[2];
        if (!isset($this->_data[$configElement])) {
            return '';
        }
        return $this->_data[$configElement];
    }

    /**
     * Data provider for testGetAddressEmptyCountry method
     *
     * @return array
     */
    public function testGetAddressWithoutCountryDataProvider()
    {
        return array(
            array(
                array(
                    'address-street-line1' => 'Zoologichna',
                    'address-street-line2' => '5 A',
                    'address-city' => 'Kiev',
                    'address-postcode' => '03344',
                )
            )
        );
    }

    /**
     * Data provider for testGetAddress method
     *
     * @return array
     */
    public function testGetAddressDataProvider()
    {
        return array(
            array(
                array(
                    'address-street-line1' => 'Zoologichna',
                    'address-street-line2' => '5 A',
                    'address-city' => 'Kiev',
                    'address-postcode' => '03344',
                    'address-region-name' =>'Alaska',
                    'address-country-name' => 'United States',
                ),
                array(
                    'Alaska',
                    'California',
                    'Florida',
                )
            ),
            array(
                array(
                    'address-street-line1' => 'Zoologichna',
                    'address-street-line2' => '5 A',
                    'address-city' => 'Kiev',
                    'address-postcode' => '03344',
                    'address-region-name' => 5,
                    'address-country-name' => 'United States',
                ),
                array()
            )
        );
    }

    /**
     * Data provider for testIsBusinessAddressConfigured method
     *
     * @return array
     */
    public function testIsBusinessAddressConfiguredDataProvider()
    {
        return array(
            array(
                true,
                array(
                    'Alaska',
                    'California',
                    'Florida',
                )
            ),
            array(
                true,
                array()
            )
        );
    }

    /**
     * Data provider for testIsBusinessAddressNotConfigured method
     *
     * @return array
     */
    public function testIsBusinessAddressNotConfiguredDataProvider()
    {
        return array(
            array(
                array(
                    'name' => 'Magento',
                    'street_line2' => '5 A',
                    'city' => 'Kiev',
                    'postcode' => '03344',
                    'region_id' => 5,
                    'country_id' => 'US',
                    'email' => 'test@example.com',
                    'name' => 'Store Name',
                ),
                array(
                    'Alaska',
                    'California',
                    'Florida',
                )
            ),
            array(
                array(
                    'name' => 'Magento',
                    'street_line1' => 'Zoologichna',
                    'street_line2' => '5 A',
                    'postcode' => '03344',
                    'region_id' => 5,
                    'country_id' => 'US',
                    'email' => 'test@example.com',
                    'name' => 'Store Name',
                ),
                array()
            )
        );
    }

    public function testGetStoreName()
    {
        $this->assertEquals('Store Name', $this->_tileBlock->getStoreName());
    }

    public function testGetGeneralEmail()
    {
        $this->assertEquals('test@example.com', $this->_tileBlock->getGeneralEmail());
    }
}
