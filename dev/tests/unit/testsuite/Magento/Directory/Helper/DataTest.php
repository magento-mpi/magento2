<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Directory_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Directory_Model_Resource_Country_Collection|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_countryCollection;

    /**
     * @var Magento_Directory_Model_Resource_Region_CollectionFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_regionCollection;

    /**
     * @var Magento_Core_Helper_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreHelper;

    /**
     * @var Magento_Core_Model_Store|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_store;

    /**
     * @var Magento_Directory_Helper_Data
     */
    protected $_object;

    public function setUp()
    {
        $objectManager = new Magento_TestFramework_Helper_ObjectManager($this);
        $context = $this->getMock('Magento_Core_Helper_Context', array(), array(), '', false);

        $configCacheType = $this->getMock('Magento_Core_Model_Cache_Type_Config', array(), array(), '', false);

        $this->_countryCollection = $this->getMock('Magento_Directory_Model_Resource_Country_Collection', array(),
            array(), '', false);

        $this->_regionCollection = $this->getMock('Magento_Directory_Model_Resource_Region_Collection', array(),
            array(), '', false);
        $regCollFactory = $this->getMock('Magento_Directory_Model_Resource_Region_CollectionFactory', array('create'),
            array(), '', false);
        $regCollFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_regionCollection));

        $this->_coreHelper = $this->getMock('Magento_Core_Helper_Data', array(), array(), '', false);

        $this->_store = $this->getMock('Magento_Core_Model_Store', array(), array(), '', false);
        $storeManager = $this->getMock('Magento_Core_Model_StoreManagerInterface', array(), array(), '', false);
        $storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($this->_store));

        $currencyFactory = $this->getMock('Magento_Directory_Model_CurrencyFactory', array(), array(), '', false);

        $arguments = array(
            'context' => $context,
            'configCacheType' => $configCacheType,
            'countryCollection' => $this->_countryCollection,
            'regCollFactory' => $regCollFactory,
            'coreHelper' => $this->_coreHelper,
            'storeManager' => $storeManager,
            'currencyFactory' => $currencyFactory,
            'config' => $this->getMock('Magento_Core_Model_Config', array(), array(), '', false),
        );
        $this->_object = $objectManager->getObject('Magento_Directory_Helper_Data', $arguments);
    }

    public function testGetRegionJson()
    {
        $countries = array(
            new Magento_Object(array('country_id' => 'Country1')),
            new Magento_Object(array('country_id' => 'Country2')),
        );
        $countryIterator = new ArrayIterator($countries);
        $this->_countryCollection->expects($this->atLeastOnce())
            ->method('getIterator')
            ->will($this->returnValue($countryIterator));

        $regions = array(
            new Magento_Object(
                array('country_id' => 'Country1', 'region_id' => 'r1', 'code' => 'r1-code', 'name' => 'r1-name')
            ),
            new Magento_Object(
                array('country_id' => 'Country1', 'region_id' => 'r2', 'code' => 'r2-code', 'name' => 'r2-name')
            ),
            new Magento_Object(
                array('country_id' => 'Country2', 'region_id' => 'r3', 'code' => 'r3-code', 'name' => 'r3-name')
            ),
        );
        $regionIterator = new ArrayIterator($regions);

        $this->_regionCollection->expects($this->once())
            ->method('addCountryFilter')
            ->with(array('Country1', 'Country2'))
            ->will($this->returnSelf());
        $this->_regionCollection->expects($this->once())
            ->method('load');
        $this->_regionCollection->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue($regionIterator));

        $expectedDataToEncode = array(
            'config' => array(
                'show_all_regions' => false,
                'regions_required' => array(),
            ),
            'Country1' => array(
                'r1' => array(
                    'code' => 'r1-code',
                    'name' => 'r1-name',
                ),
                'r2' => array(
                    'code' => 'r2-code',
                    'name' => 'r2-name',
                ),
            ),
            'Country2' => array(
                'r3' => array(
                    'code' => 'r3-code',
                    'name' => 'r3-name',
                ),
            ),
        );
        $this->_coreHelper->expects($this->once())
            ->method('jsonEncode')
            ->with(new PHPUnit_Framework_Constraint_IsIdentical($expectedDataToEncode))
            ->will($this->returnValue('encoded_json'));

        // Test
        $result = $this->_object->getRegionJson();
        $this->assertEquals('encoded_json', $result);
    }

    /**
     * @param string $configValue
     * @param mixed $expected
     * @dataProvider countriesCommaListDataProvider
     */
    public function testGetCountriesWithStatesRequired($configValue, $expected)
    {
        $this->_store->expects($this->once())
            ->method('getConfig')
            ->with('general/region/state_required')
            ->will($this->returnValue($configValue));

        $result = $this->_object->getCountriesWithStatesRequired();
        $this->assertEquals($expected, $result);
    }

    /**
     * @param string $configValue
     * @param mixed $expected
     * @dataProvider countriesCommaListDataProvider
     */
    public function testGetCountriesWithOptionalZip($configValue, $expected)
    {
        $this->_store->expects($this->once())
            ->method('getConfig')
            ->with('general/country/optional_zip_countries')
            ->will($this->returnValue($configValue));

        $result = $this->_object->getCountriesWithOptionalZip();
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public static function countriesCommaListDataProvider()
    {
        return array(
            'empty_list' => array(
                '',
                array(),
            ),
            'normal_list' => array(
                'Country1,Country2',
                array('Country1', 'Country2'),
            ),
        );
    }
}
