<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_ImportExport_Model_Export_Entity_EavAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Abstract eav export model
     *
     * @var Magento_ImportExport_Model_Export_Entity_EavAbstract|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * Attribute codes for tests
     *
     * @var array
     */
    protected $_expectedAttributes = array('firstname', 'lastname');

    public function setUp()
    {
        $this->_model = $this->getMockForAbstractClass('Magento_ImportExport_Model_Export_Entity_EavAbstract', array(),
            '', false, true, true, array('_getExportAttributeCodes', 'getAttributeCollection', 'getAttributeOptions'));

        $this->_model->expects($this->once())
            ->method('_getExportAttributeCodes')
            ->will($this->returnValue($this->_expectedAttributes));
    }

    public function tearDown()
    {
        unset($this->_model);
    }

    /**
     * Test for method _addAttributesToCollection()
     *
     * @covers Magento_ImportExport_Model_Export_Entity_EavAbstract::_addAttributesToCollection
     */
    public function testAddAttributesToCollection()
    {
        $method = new ReflectionMethod($this->_model, '_addAttributesToCollection');
        $method->setAccessible(true);
        $stubCollection = $this->getMock(
            'Magento_Eav_Model_Entity_Collection_Abstract', array('addAttributeToSelect'), array(), '', false
        );
        $stubCollection->expects($this->once())->method('addAttributeToSelect')->with($this->_expectedAttributes);
        $method->invoke($this->_model, $stubCollection);
    }

    /**
     * Test for methods _addAttributeValuesToRow()
     *
     * @covers Magento_ImportExport_Model_Export_Entity_EavAbstract::_initAttrValues
     * @covers Magento_ImportExport_Model_Export_Entity_EavAbstract::_addAttributeValuesToRow
     */
    public function testAddAttributeValuesToRow()
    {
        $testAttributeCode = 'lastname';
        $testAttributeValue = 'value';
        $testAttributeOptions = array('value' => 'option');
        /** @var $testAttribute Magento_Eav_Model_Entity_Attribute */
        $testAttribute = $this->getMockForAbstractClass('Magento_Eav_Model_Entity_Attribute_Abstract', array(), '',
            false);
        $testAttribute->setAttributeCode($testAttributeCode);

        $this->_model->expects($this->any())
            ->method('getAttributeCollection')
            ->will($this->returnValue(array($testAttribute)));

        $this->_model->expects($this->any())
            ->method('getAttributeOptions')
            ->will($this->returnValue($testAttributeOptions));

        /** @var $item Magento_Core_Model_Abstract|PHPUnit_Framework_MockObject_MockObject */
        $item = $this->getMockForAbstractClass('Magento_Core_Model_Abstract', array(), '', false, true, true,
            array('getData'));
        $item->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($testAttributeValue));

        $method = new ReflectionMethod($this->_model, '_initAttributeValues');
        $method->setAccessible(true);
        $method->invoke($this->_model);

        $method = new ReflectionMethod($this->_model, '_addAttributeValuesToRow');
        $method->setAccessible(true);
        $row = $method->invoke($this->_model, $item);
        /**
         *  Prepare expected data
         */
        $expected = array();
        foreach ($this->_expectedAttributes as $code) {
            $expected[$code] = $testAttributeValue;
            if ($code == $testAttributeCode) {
                $expected[$code] = $testAttributeOptions[$expected[$code]];
            }
        }

        $this->assertEquals($expected, $row, 'Attributes were not added to result row');
    }
}
