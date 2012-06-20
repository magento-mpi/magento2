<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address
 */
class Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_AddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * Abstract customer export model
     *
     * @var Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * Customers array
     *
     * @var array
     */
    protected $_regions = array(
        array(
            'id'           => 1,
            'country_id'   => 1,
            'code'         => 'code1',
            'default_name' => 'name1',
        ),
        array(
            'id'           => 2,
            'country_id'   => 1,
            'code'         => 'code2',
            'default_name' => 'name2',
        ),
    );

    public function setUp()
    {
        parent::setUp();

        $this->_model = $this->_getModelMock();
    }

    public function tearDown()
    {
        unset($this->_model);

        parent::tearDown();
    }

    /**
     * Create mock for customer address model class
     */
    protected function _getModelMock()
    {
        $regionCollection = new Varien_Data_Collection();
        foreach ($this->_regions as $region) {
            $regionCollection->addItem(new Varien_Object($region));
        }

        $modelMock = $this->getMock('Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address',
            array('_getRegionCollection'), array(), '', false, true, true
        );

        $modelMock->expects($this->any())
            ->method('_getRegionCollection')
            ->will($this->returnValue($regionCollection));

        return $modelMock;
    }

    /**
     * Check whether Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address::_regions and
     * Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address::_countryRegions are filled correctly
     *
     * @covers Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address::_initCountryRegions()
     */
    public function testInitCountryRegions()
    {
        $regions = array();
        $countryRegions = array();
        foreach ($this->_regions as $region) {
            $countryNormalized = strtolower($region['country_id']);
            $regionCode = strtolower($region['code']);
            $regionName = strtolower($region['default_name']);
            $countryRegions[$countryNormalized][$regionCode] = $region['id'];
            $countryRegions[$countryNormalized][$regionName] = $region['id'];
            $regions[$region['id']] = $region['default_name'];
        }

        $method = new ReflectionMethod($this->_model, '_initCountryRegions');
        $method->setAccessible(true);
        $method->invoke($this->_model);

        $this->assertAttributeEquals($regions, '_regions', $this->_model);
        $this->assertAttributeEquals($countryRegions, '_countryRegions', $this->_model);
    }
}
