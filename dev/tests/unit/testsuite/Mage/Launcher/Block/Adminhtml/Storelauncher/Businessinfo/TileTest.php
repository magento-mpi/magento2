<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Tile
 */
class Mage_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_TileTest extends PHPUnit_Framework_TestCase
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
     * @var Mage_Core_Model_Store_Config
     */
    protected $_config;

    public function setUp()
    {
        $this->_data = array(
            'street_line1' => 'Zoologichna',
            'street_line2' => '5 A',
            'city' => 'Kiev',
            'postcode' => '03344',
            'region_id' => 5,
            'country_id' => 'US',
            'email' => 'test@example.com',
        );

        $this->_config = $this->getMock('Mage_Core_Model_Store_Config', array('getConfig'), array(), '', false);
        $this->_config->expects($this->any())
            ->method('getConfig')
            ->will($this->returnCallback(array($this, 'configCallback')));
    }

    /**
     * @dataProvider testGetAddressDataProvider
     * @param array $expectedData
     * @param array $regions Array of Regions for Current Country
     */
    public function testGetAddress($expectedData, $regions)
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);

        $countryModel = $this->getMock('Mage_Directory_Model_Country',
            array('loadByCode', 'getName'), array(), '', false
        );

        $countryModel->expects($this->once())
            ->method('loadByCode')
            ->with($this->_data['country_id'])
            ->will($this->returnValue($countryModel));

        $countryModel->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('United States'));

        $regionModel = $this->getMock('Mage_Directory_Model_Region',
            array('load', 'getName', 'getCollection'), array(), '', false
        );

        $collectionMock = $this->getMock('Mage_Directory_Model_Resource_Region',
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
            'Mage_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Tile',
            $arguments
        );

        $result = $tileBlock->getAddress();
        $this->assertEquals($expectedData, $result);
    }

    /**
     * @dataProvider testGetAddressWithoutCountryDataProvider
     * @param array $expectedData
     * @covers Mage_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Tile::getAddress
     */
    public function testGetAddressEmptyCountry(array $expectedData)
    {
        //Remove country dependent data
        unset($this->_data['country_id']);
        unset($this->_data['region_id']);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);

        $countryModel = $this->getMock('Mage_Directory_Model_Country', array(), array(), '', false);
        $regionModel = $this->getMock('Mage_Directory_Model_Region', array(), array(), '', false);

        $arguments = array(
            'storeConfig' => $this->_config,
            'countryModel' => $countryModel,
            'regionModel' => $regionModel,
        );

        $tileBlock = $objectManagerHelper->getObject(
            'Mage_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Tile',
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
                    'Zoologichna',
                    '5 A',
                    'Kiev',
                    '03344',
                    'test@example.com'
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
                    'Zoologichna',
                    '5 A',
                    'Kiev',
                    '03344',
                    'Alaska',
                    'United States',
                    'test@example.com'
                ),
                array(
                    'Alaska',
                    'California',
                    'Florida',
                )
            ),
            array(
                array(
                    'Zoologichna',
                    '5 A',
                    'Kiev',
                    '03344',
                    5,
                    'United States',
                    'test@example.com'
                ),
                array()
            )
        );
    }
}
