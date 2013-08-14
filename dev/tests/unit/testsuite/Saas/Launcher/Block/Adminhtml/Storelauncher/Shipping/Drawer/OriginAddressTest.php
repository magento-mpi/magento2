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
 * Test class for Saas_Launcher_Block_Adminhtml_Storelauncher_Shipping_Drawer_OriginAddress
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Shipping_Drawer_OriginAddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * Origin Address Block
     *
     * @var Saas_Launcher_Block_Adminhtml_Storelauncher_Shipping_Drawer_OriginAddress
     */
    protected $_addressBlock;

    /**
     * Expected data array, used in configCallback method
     *
     * @var array
     */
    protected $_sourceData;

    public function setUp()
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $config = $this->getMock('Magento_Core_Model_Store_Config', array('getConfig'), array(), '', false);
        $config->expects($this->any())
            ->method('getConfig')
            ->will($this->returnCallback(array($this, 'configCallback')));

        $regionModel = $this->getMock('Magento_Directory_Model_Region', array(
            'getCollection',
            'addCountryFilter',
            'toOptionArray',
            'load',
            'getName',
        ), array(), '', false);
        $regionModel->expects($this->any())
            ->method('getCollection')
            ->will($this->returnValue($regionModel));
        $regionModel->expects($this->any())
            ->method('addCountryFilter')
            ->with('US')
            ->will($this->returnValue($regionModel));
        $regionModel->expects($this->any())
            ->method('toOptionArray')
            ->will($this->returnValue(array('code' => 'region')));
        $regionModel->expects($this->any())
            ->method('load')
            ->with(12)
            ->will($this->returnValue($regionModel));
        $regionModel->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('California'));

        $countryModel = $this->getMock('Magento_Directory_Model_Country', array(), array(), '', false);
        $countryModel->expects($this->any())
            ->method('loadByCode')
            ->with('US')
            ->will($this->returnValue($countryModel));
        $countryModel->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('United States'));

        $arguments = array(
            'storeConfig' => $config,
            'regionModel' => $regionModel,
            'countryModel' => $countryModel,
        );

        $this->_addressBlock = $objectManagerHelper->getObject(
            'Saas_Launcher_Block_Adminhtml_Storelauncher_Shipping_Drawer_OriginAddress',
            $arguments
        );

        $this->_sourceData = array();
    }

    public function tearDown()
    {
        unset($this->_addressBlock);
        unset($this->_sourceData);
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
        return $this->_sourceData[$configPath[2]];
    }

    /**
     * @dataProvider testGetAddressDataDataProvider
     * @param array $expectedData
     */
    public function testGetAddressData($expectedData)
    {
        $this->_sourceData = $expectedData;
        $result = $this->_addressBlock->getAddressData();

        $this->assertEquals($result, $expectedData);
    }

    /**
     * Data provider for testGetAddressData method
     *
     * @return array
     */
    public function testGetAddressDataDataProvider()
    {
        return array(
            array(
                array(
                    'street_line1' => '10441 Jefferson Blvd.',
                    'street_line2' => 'Suite 200',
                    'city' => 'Culver City',
                    'postcode' => '90232',
                    'region_id' => 12,
                    'country_id' => 'US'
                )
            )
        );
    }

    /**
     * @dataProvider testGetAddressDataProvider
     * @param array $sourceData
     * @param array $expectedData
     */
    public function testGetAddress($sourceData, $expectedData)
    {
        $this->_sourceData = $sourceData;
        $result = $this->_addressBlock->getAddress();

        $this->assertEquals($result, $expectedData);
    }

    /**
     * Data provider for testGetAddress method
     *
     * @return array
     */
    public function testGetAddressDataProvider()
    {
        $configAddress1 = array(
            'street_line1' => '10441 Jefferson Blvd.',
            'street_line2' => 'Suite 200',
            'city' => 'Culver City',
            'postcode' => '90232',
            'region_id' => 12,
            'country_id' => 'US'
        );
        $expectedAddress1 = array(
            'show_form' => false,
            'data' => array(
                'street_line1' => '10441 Jefferson Blvd.',
                'street_line2' => 'Suite 200',
                'city' => 'Culver City',
                'postcode' => '90232',
                'region_id' => 'California',
                'country_id' => 'United States'
            )
        );

        $configAddress2 = $configAddress1;
        $configAddress2['city'] = null;

        $expectedAddress2 = $expectedAddress1;
        $expectedAddress2['data']['city'] = null;
        $expectedAddress2['show_form'] = true;

        $configAddress3 = $configAddress2;
        $configAddress3['country_id'] = '';
        $configAddress3['region_id'] = '';

        $expectedAddress3 = $expectedAddress2;
        $expectedAddress3['data']['country_id'] = '';
        $expectedAddress3['data']['region_id'] = '';

        return array(
            array($configAddress1, $expectedAddress1),
            array($configAddress2, $expectedAddress2),
            array($configAddress3, $expectedAddress3),
        );
    }
}
