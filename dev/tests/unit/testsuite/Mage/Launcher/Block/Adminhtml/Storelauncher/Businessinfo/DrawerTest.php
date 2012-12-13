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

    public function setUp()
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $config = $this->getMock('Mage_Core_Model_Store_Config', array('getConfig'), array(), '', false);
        $config->expects($this->any())
            ->method('getConfig')
            ->will($this->returnCallback(array($this, 'configCallback')));

        $regionModel = $this->getMock('Mage_Directory_Model_Region',
            array('loadByName', 'getRegionId'), array(), '', false
        );

        $regionModel->expects($this->any())
            ->method('loadByName')
            ->will($this->returnValue($regionModel));

        $regionModel->expects($this->any())
            ->method('getRegionId')
            ->will($this->returnValue(5));

        $arguments = array(
            'storeConfig' => $config,
            'regionModel' => $regionModel,
            'urlBuilder' => $this->getMock('Mage_Backend_Model_Url', array(), array(), '', false),
            'countryModel' => $this->getMock('Mage_Directory_Model_Config_Source_Country', array(), array(), '', false),
            'validateVat' => $this->getMock('Mage_Adminhtml_Block_Customer_System_Config_ValidatevatFactory',
                array(), array(), '', false
            )
        );

        $this->_drawerBlock = $objectManagerHelper->getBlock(
            'Mage_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Drawer',
            $arguments
        );
    }

    public function tearDown()
    {
        unset($this->_drawerBlock);
    }

    /**
     * Callback function for getConfig method
     *
     * @param string $param
     * @return string
     */
    public function configCallback($param)
    {
        if ($param == 'general/store_information/address') {
            return "Zoologichna\n5 A\nLos Angeles\n03344\n5";
        }
        if ($param == 'general/store_information/merchant_country') {
            return 'US';
        }
        $param = str_replace('shipping/origin/', '', $param);
        $result = '';
        switch ($param) {
            case 'street_line1':
                $result = 'Zoologichna';
                break;
            case 'street_line2':
                $result = '5 A';
                break;
            case 'city':
                $result = 'Los Angeles';
                break;
            case 'postcode':
                $result = '03344';
                break;
            case 'region_id':
                $result = 5;
                break;
            case 'country_id':
                $result = 'US';
                break;
        }
        return $result;
    }

    public function testGetAddressData()
    {
        $this->assertInstanceOf('Mage_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Drawer', $this->_drawerBlock);
        $res = $this->_drawerBlock->getAddressData();

        $this->assertEquals(
            $res,
            array(
                'street_line1' => 'Zoologichna',
                'street_line2' => '5 A',
                'city' => 'Los Angeles',
                'postcode' => '03344',
                'region_id' => 5,
                'country_id' => 'US',
                'use_for_shipping' => 1
            )
        );
    }
}
