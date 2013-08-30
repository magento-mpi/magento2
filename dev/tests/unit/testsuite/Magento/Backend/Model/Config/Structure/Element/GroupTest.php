<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_Structure_Element_GroupTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Model_Config_Structure_Element_Group
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_applicationMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cloneFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_iteratorMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_depMapperMock;

    protected function setUp()
    {
        $this->_iteratorMock = $this->getMock(
            'Magento_Backend_Model_Config_Structure_Element_Iterator_Field', array(), array(), '', false
        );
        $this->_applicationMock = $this->getMock('Magento_Core_Model_App', array(), array(), '', false);
        $this->_cloneFactoryMock = $this->getMock(
            'Magento_Backend_Model_Config_Clone_Factory', array(), array(), '', false
        );
        $this->_depMapperMock = $this->getMock(
            'Magento_Backend_Model_Config_Structure_Element_Dependency_Mapper', array(), array(), '', false
        );

        $this->_model = new Magento_Backend_Model_Config_Structure_Element_Group(
            $this->_applicationMock,
            $this->_iteratorMock, $this->_cloneFactoryMock,
            $this->_depMapperMock
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_iteratorMock);
        unset($this->_applicationMock);
        unset($this->_cloneFactoryMock);
        unset($this->_depMapperMock);
    }

    public function testShouldCloneFields()
    {
        $this->assertFalse($this->_model->shouldCloneFields());
        $this->_model->setData(array('clone_fields' => 1), 'scope');
        $this->assertTrue($this->_model->shouldCloneFields());
        $this->_model->setData(array('clone_fields' => 0), 'scope');
        $this->assertFalse($this->_model->shouldCloneFields());
        $this->_model->setData(array('clone_fields' => false), 'scope');
        $this->assertFalse($this->_model->shouldCloneFields());
    }

    /**
     * @expectedException Magento_Core_Exception
     */
    public function testGetCloneModelThrowsExceptionIfNoSourceModelIsSet()
    {
        $this->_model->getCloneModel();
    }

    public function testGetCloneModelCreatesCloneModel()
    {
        $cloneModel = $this->getMock('Mage_Core_Model_Config_Value', array(), array(), '', false);
        $this->_depMapperMock = $this->getMock(
            'Magento_Backend_Model_Config_Structure_Element_Dependency_Mapper', array(), array(), '', false
        );
        $this->_cloneFactoryMock->expects($this->once())->method('create')
            ->with('clone_model_name')
            ->will($this->returnValue($cloneModel));
        $this->_model->setData(array('clone_model' => 'clone_model_name'), 'scope');
        $this->assertEquals($cloneModel, $this->_model->getCloneModel());
    }

    public function testGetFieldsetSetsOnlyNonArrayValuesToFieldset()
    {
        $fieldsetMock = $this->getMock(
            'Magento_Data_Form_Element_Fieldset', array('setOriginalData'), array(), '', false
        );
        $fieldsetMock->expects($this->once())->method('setOriginalData')->with(array(
            'var1' => 'val1',
            'var2' => 'val2'
        ));

        $this->_model->setData(array('var1' => 'val1', 'var2' => 'val2', 'var3' => array('val3')), 'scope');
        $this->_model->populateFieldset($fieldsetMock);
    }

    public function testIsExpanded()
    {
        $this->assertFalse($this->_model->isExpanded());
        $this->_model->setData(array('expanded' => 1), 'scope');
        $this->assertTrue($this->_model->isExpanded());
        $this->_model->setData(array('expanded' => 0), 'scope');
        $this->assertFalse($this->_model->isExpanded());
        $this->_model->setData(array('expanded' => null), 'scope');
        $this->assertFalse($this->_model->isExpanded());
    }

    public function testGetFieldsetCss()
    {
        $this->assertEquals('', $this->_model->getFieldsetCss());
        $this->_model->setData(array('fieldset_css' => 'some_css'), 'scope');
        $this->assertEquals('some_css', $this->_model->getFieldsetCss());
    }

    public function testGetDependenciesWithoutDependencies()
    {
        $this->_depMapperMock->expects($this->never())->method('getDependencies');
    }

    public function testGetDependenciesWithDependencies()
    {
        $fields = array(
            'field_4' => array(
                'id' => 'section_2/group_3/field_4',
                'value' => 'someValue',
                'dependPath' => array(
                    'section_2',
                    'group_3',
                    'field_4'
                ),
            ),
        );
        $this->_model->setData(array('depends' => array('fields' => $fields)), 0);
        $this->_depMapperMock->expects($this->once())
            ->method('getDependencies')->with($fields, 'test_scope')
            ->will($this->returnArgument(0));

        $this->assertEquals($fields, $this->_model->getDependencies('test_scope'));
    }
}
