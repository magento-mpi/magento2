<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_Element_Dependency_MapperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Config_Structure_Element_Dependency_Mapper
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_applicationMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configStructureMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fieldMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeMock;

    /**
     * Test data
     *
     * @var array
     */
    protected $_testData;

    public function setUp()
    {
        $this->_applicationMock = $this->getMock('Mage_Core_Model_App', array(), array(), '', false);
        $this->_configStructureMock = $this->getMock(
            'Mage_Backend_Model_Config_Structure', array(), array(), '', false
        );
        $this->_fieldMock = $this->getMock(
            'Mage_Backend_Model_Config_Structure_Element_Field', array(), array(), '', false
        );

        $this->_storeMock = $this->getMock(
            'Mage_Core_Model_Store', array(), array(), '', false
        );

        $this->_testData = array(
            'field_4' => array(
                'id' => 'section_2/group_3/field_4',
                'value' => 'someValue',
                'dependPath' => array(
                    'section_2',
                    'group_3',
                    'field_4',
                ),
            ),
        );

        $this->_configStructureMock->expects($this->once())
            ->method('getElement')->with('section_2/group_3/field_4')->will($this->returnValue($this->_fieldMock));

        $this->_model = new Mage_Backend_Model_Config_Structure_Element_Dependency_Mapper(
            $this->_applicationMock,
            $this->_configStructureMock
        );
    }

    public function testGetDependenciesWhenDependentValueIsNotEqualValueInStoreAndDependentIsInvisible()
    {
        $this->_applicationMock->expects($this->once())
            ->method('getStore')->with('store_code')->will($this->returnValue($this->_storeMock));

        $this->_fieldMock->expects($this->once())
            ->method('getPath')->with('prefix')->will($this->returnValue('field_path'));

        $this->_fieldMock->expects($this->once())->method('isVisible')->will($this->returnValue(false));

        $this->_storeMock->expects($this->once())->method('getConfig')->with('field_path')->will($this->returnValue(1));

        $actual = $this->_model->getDependencies($this->_testData, 'store_code', 'prefix');

        $expected = array('section_2_group_3_prefixfield_4' => 'someValue');

        $this->assertEquals($expected, $actual);
    }

    public function testGetDependenciesWhenDependentValueIsArray()
    {
        $testData = array(
            'field_4' => array(
                'id' => 'section_2/group_3/field_4',
                'value' => 'value1,value2',
                'separator' => ',',
                'dependPath' => array(
                    'section_2',
                    'group_3',
                    'field_4',
                ),
            ),
        );
        $this->_applicationMock->expects($this->once())
            ->method('getStore')->with('store_code')->will($this->returnValue($this->_storeMock));

        $this->_fieldMock->expects($this->once())
            ->method('getPath')->with('prefix')->will($this->returnValue('field_path'));

        $this->_fieldMock->expects($this->once())->method('isVisible')->will($this->returnValue(false));

        $this->_storeMock->expects($this->once())
            ->method('getConfig')->with('field_path')->will($this->returnValue('value2'));

        $actual = $this->_model->getDependencies($testData, 'store_code', 'prefix');

        $expected = array();

        $this->assertEquals($expected, $actual);
    }

    public function testGetDependenciesWhenDependentValueIsEqualValueInStoreAndDependentIsInvisible()
    {
        $this->_fieldMock->expects($this->once())->method('isVisible')->will($this->returnValue(false));

        $this->_applicationMock->expects($this->once())
            ->method('getStore')->with('store_code')->will($this->returnValue($this->_storeMock));

        $this->_fieldMock->expects($this->once())
            ->method('getPath')->with('prefix')->will($this->returnValue('field_path'));

        $this->_storeMock->expects($this->once())
            ->method('getConfig')->with('field_path')->will($this->returnValue('someValue'));

        $actual = $this->_model->getDependencies($this->_testData, 'store_code', 'prefix');

        $expected = array();

        $this->assertEquals($expected, $actual);
    }

    public function testGetDependenciesIsVisible()
    {
        $this->_fieldMock->expects($this->once())->method('isVisible')->will($this->returnValue(true));

        $actual = $this->_model->getDependencies($this->_testData, 'store_code', 'prefix');

        $expected = array('section_2_group_3_prefixfield_4' => 'someValue');

        $this->assertEquals($expected, $actual);
    }

    public function testGetDependenciesWithSeparator()
    {
        $testData = array(
            'field_4' => array(
                'id' => 'section_2/group_3/field_4',
                'value' => 'value1,value2',
                'separator' => ',',
                'dependPath' => array(
                    'section_2',
                    'group_3',
                    'field_4',
                ),
            ),
        );
        $this->_fieldMock->expects($this->once())->method('isVisible')->will($this->returnValue(true));

        $actual = $this->_model->getDependencies($testData, 'store_code', 'prefix');

        $expected = array('section_2_group_3_prefixfield_4' => array('value1', 'value2'));

        $this->assertEquals($expected, $actual);
    }
}
