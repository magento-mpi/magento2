<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Saas_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Drawer
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_DrawerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Drawer Block
     *
     * @var Saas_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Drawer
     */
    protected $_drawerBlock;

    /**
     * Configuration map
     *
     * @var array
     */
    protected $_configMap;

    public function setUp()
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $config = $this->getMock('Magento_Core_Model_Store_Config', array('getConfig'), array(), '', false);
        $config->expects($this->any())
            ->method('getConfig')
            ->will($this->returnCallback(array($this, 'configCallback')));

        $regionModel = $this->getMock('Mage_Directory_Model_Region', array(), array(), '', false);

        $arguments = array(
            'storeConfig' => $config,
            'regionModel' => $regionModel,
        );

        $this->_drawerBlock = $objectManagerHelper->getObject(
            'Saas_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Drawer',
            $arguments
        );

        $this->_configMap = array();
    }

    public function tearDown()
    {
        unset($this->_drawerBlock);
        unset($this->_configMap);
    }

    /**
     * Callback function for getConfig method
     *
     * @param string $configPath
     * @return string
     */
    public function configCallback($configPath)
    {
        return $this->_configMap[$configPath];
    }

    /**
     * @dataProvider testGetAddressDataProvider
     * @param array $expectedResult
     * @param array $configMap
     * @param boolean $tileState
     */
    public function testGetAddressData(array $expectedResult, array $configMap, $tileState)
    {
        $this->_configMap = $configMap;
        // mock tile instance
        /** @var $tile PHPUnit_Framework_MockObject_MockObject|Saas_Launcher_Model_Tile */
        $tile = $this->getMock('Saas_Launcher_Model_Tile', array('getState'), array(), '', false);
        $tile->expects($this->any())
            ->method('getState')
            ->will($this->returnValue($tileState));
        $this->_drawerBlock->setTile($tile);

        $this->assertEquals($expectedResult, $this->_drawerBlock->getAddressData());
    }

    /**
     * Data provider for testGetAddressData method
     *
     * @return array
     */
    public function testGetAddressDataProvider()
    {
        $baseConfigMap = array(
            // business address data
            'general/store_information/street_line1' => 'In the middle of nowhere',
            'general/store_information/street_line2' => '5A',
            'general/store_information/city' => 'Los Angeles',
            'general/store_information/postcode' => '03344',
            'general/store_information/region_id' => 5,
            'general/store_information/country_id' => 'US',
            'trans_email/ident_general/email' => 'owner@example.com',
            'general/store_information/name' => 'Store Name',
            'general/store_information/phone' => '1234567890',
            // shipping origin data
            'shipping/origin/street_line1' => 'In the middle of nowhere',
            'shipping/origin/street_line2' => '5A',
            'shipping/origin/city' => 'Los Angeles',
            'shipping/origin/postcode' => '03344',
            'shipping/origin/region_id' => 5,
            'shipping/origin/country_id' => 'US',
        );

        $baseExpectedResult = array(
            'street_line1' => 'In the middle of nowhere',
            'street_line2' => '5A',
            'city' => 'Los Angeles',
            'postcode' => '03344',
            'country_id' => 'US',
            'region_id' => 5,
            'use_for_shipping' => true,
            'name' => 'Store Name',
            'phone' => '1234567890',
            'email' => 'owner@example.com',
        );

        $configMap0 = $baseConfigMap;
        $expectedResult0 = $baseExpectedResult;

        $configMap1 = $baseConfigMap;
        $configMap1['shipping/origin/street_line1'] = 'value different from business address';
        $expectedResult1 = $baseExpectedResult;
        $expectedResult1['use_for_shipping'] = false;

        $configMap2 = $configMap1;
        $expectedResult2 = $baseExpectedResult;


        return array(
            array($expectedResult0, $configMap0, Saas_Launcher_Model_Tile::STATE_COMPLETE),
            array($expectedResult1, $configMap1, Saas_Launcher_Model_Tile::STATE_COMPLETE),
            array($expectedResult2, $configMap2, Saas_Launcher_Model_Tile::STATE_TODO),
        );
    }
}
