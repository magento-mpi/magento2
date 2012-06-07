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

class Mage_ImportExport_Model_Export_Entity_V2_Eav_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Customer export Eav model
     *
     * @var Mage_ImportExport_Model_Export_Entity_V2_Eav_Abstract|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;
    /**
     * Attribute codes for tests
     *
     * @var array
     */
    protected $_expectedAttrs = array('firstname', 'lastname');

    public function setUp()
    {
        parent::setUp();
        $this->_model = $this->getMockForAbstractClass('Mage_ImportExport_Model_Export_Entity_V2_Eav_Abstract', array(),
            '', false, true, true, array('_getExportAttrCodes', 'getAttributeCollection', 'getAttributeOptions'));

        $this->_model->expects($this->once())
            ->method('_getExportAttrCodes')
            ->will($this->returnValue($this->_expectedAttrs));
    }

    public function tearDown()
    {
        unset($this->_model);
        parent::tearDown();
    }

    /**
     * Test for method _addAttributesToCollection()
     *
     * @covers Mage_ImportExport_Model_Export_Entity_V2_Eav_Abstract::_addAttributesToCollection
     */
    public function testAddAttributesToCollection()
    {
        $method = new ReflectionMethod($this->_model, '_addAttributesToCollection');
        $method->setAccessible(true);
        $stubCollection = new Stub_ImportExport_Model_Export_Entity_V2_Eav_Collection();
        $stubCollection = $method->invoke($this->_model, $stubCollection);

        $this->assertEquals($this->_expectedAttrs, $stubCollection->getSelectedAttributes());
    }

    /**
     * Test for methods _addAttributeValuesToRow()
     *
     * @covers Mage_ImportExport_Model_Export_Entity_V2_Eav_Abstract::_initAttrValues
     * @covers Mage_ImportExport_Model_Export_Entity_V2_Eav_Abstract::_addAttributeValuesToRow
     */
    public function testAddAttributeValuesToRow()
    {
        $testAttributeCode = 'lastname';
        $testAttributeValue = 'value';
        $testAttributeOptions = array('value' => 'option');
        /** @var $testAttribute Mage_Eav_Model_Entity_Attribute */
        $testAttribute = $this->getMockForAbstractClass('Mage_Eav_Model_Entity_Attribute_Abstract', array(), '', false);
        $testAttribute->setAttributeCode($testAttributeCode);

        $this->_model->expects($this->any())
            ->method('getAttributeCollection')
            ->will($this->returnValue(array($testAttribute)));

        $this->_model->expects($this->any())
            ->method('getAttributeOptions')
            ->will($this->returnValue($testAttributeOptions));

        /** @var $item Mage_Core_Model_Abstract|PHPUnit_Framework_MockObject_MockObject */
        $item = $this->getMockForAbstractClass('Mage_Core_Model_Abstract', array(), '', false, true, true,
            array('getData'));
        $item->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($testAttributeValue));

        $method = new ReflectionMethod($this->_model, '_initAttrValues');
        $method->setAccessible(true);
        $method->invoke($this->_model);

        $method = new ReflectionMethod($this->_model, '_addAttributeValuesToRow');
        $method->setAccessible(true);
        $row = $method->invoke($this->_model, $item);
        /**
         *  Prepare expected data
         */
        $expected = array();
        foreach ($this->_expectedAttrs as $code) {
            $expected[$code] = $testAttributeValue;
            if ($code == $testAttributeCode) {
                $expected[$code] = $testAttributeOptions[$expected[$code]];
            }
        }

        $this->assertEquals($expected, $row, 'Attributes were not added to result row');
    }
}
/**
 * Stub class which used for test which check list of attributes which will be fetched from DB
 */
class Stub_ImportExport_Model_Export_Entity_V2_Eav_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    /**
     * Selected attribute(s)
     *
     * @var array|int|Mage_Core_Model_Config_Element|string
     */
    protected $_selectedAttrs;
    /**
     * Join type
     *
     * @var string
     */
    protected $_joinType;

    public function __construct()
    {

    }

    /**
     * Stub method which save selected attribute(s) into private variable
     *
     * @param array|int|Mage_Core_Model_Config_Element|string $attribute
     * @param bool $joinType
     * @return Stub_ImportExport_Model_Export_Entity_V2_Eav_Collection
     */
    public function addAttributeToSelect($attribute, $joinType = false)
    {
        $this->_selectedAttrs = $attribute;
        $this->_joinType = $joinType;
        return $this;
    }

    /**
     * Retrieve selected attribute(s)
     *
     * @return array|int|Mage_Core_Model_Config_Element|string
     */
    public function getSelectedAttributes()
    {
        return $this->_selectedAttrs;
    }
}
