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
 * Test class for Mage_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Drawer
 */
class Mage_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_DrawerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Drawer Block
     *
     * @var Mage_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Drawer
     */
    protected $_drawerBlock;

    /**
     * Expected data array, used in configCallback method
     *
     * @var array
     */
    protected $_expectedData;

    public function setUp()
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $config = $this->getMock('Mage_Core_Model_Store_Config', array('getConfig'), array(), '', false);
        $config->expects($this->any())
            ->method('getConfig')
            ->will($this->returnCallback(array($this, 'configCallback')));

        $regionModel = $this->getMock('Mage_Directory_Model_Region', array(), array(), '', false);

        $arguments = array(
            'storeConfig' => $config,
            'regionModel' => $regionModel,
            'urlBuilder' => $this->getMock('Mage_Backend_Model_Url', array(), array(), '', false),
            'countryModel' => $this->getMock('Mage_Directory_Model_Config_Source_Country', array(), array(), '', false),
            'validateVat' => $this->getMock('Mage_Adminhtml_Block_Customer_System_Config_ValidatevatFactory',
                array(), array(), '', false
            ),
            'linkTrackerFactory' => $this->getMock('Mage_Launcher_Model_LinkTrackerFactory',
                array(), array(), '', false
            )
        );

        $this->_drawerBlock = $objectManagerHelper->getBlock(
            'Mage_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Drawer',
            $arguments
        );

        $this->_expectedData = array();
    }

    public function tearDown()
    {
        unset($this->_drawerBlock);
        unset($this->_expectedData);
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

        $result = $this->_expectedData[$configElement];
        if (!$this->_expectedData['use_for_shipping'] && $configPath[0] == 'shipping') {
            $result .= 'shipping';
        }
        return $result;
    }

    /**
     * @dataProvider testGetAddressDataProvider
     * @param array $expectedData
     */
    public function testGetAddressData($expectedData)
    {
        $this->_expectedData = $expectedData;
        $result = $this->_drawerBlock->getAddressData();

        $this->assertEquals($result, $expectedData);
    }

    /**
     * Data provider for testGetAddressData method
     *
     * @return array
     */
    public function testGetAddressDataProvider()
    {
        return array(
            array(
                array(
                    'street_line1' => 'Zoologichna',
                    'street_line2' => '5 A',
                    'city' => 'Los Angeles',
                    'postcode' => '03344',
                    'region_id' => 5,
                    'country_id' => 'US',
                    'use_for_shipping' => true,
                    'email' => 'test@example.com',
                    'name' => 'Store Name',
                    'phone' => '1234567890',
                    'merchant_vat_number' => '123456'
                )
            ),
            array(
                array(
                    'street_line1' => 'Zoologichna',
                    'street_line2' => '5 A',
                    'city' => 'Los Angeles',
                    'postcode' => '03344',
                    'region_id' => 5,
                    'country_id' => 'US',
                    'use_for_shipping' => false,
                    'email' => 'test@example.com',
                    'name' => 'Store Name',
                    'phone' => '1234567890',
                    'merchant_vat_number' => '123456'
                )
            )
        );
    }
}
