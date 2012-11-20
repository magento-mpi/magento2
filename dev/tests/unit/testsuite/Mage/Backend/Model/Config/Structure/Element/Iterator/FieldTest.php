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

class Mage_Backend_Model_Config_Structure_Element_Iterator_FieldTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Config_Structure_Element_Iterator_Field
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fieldMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_groupMock;

    public function setUp()
    {
        $this->_fieldMock = $this->getMock(
            'Mage_Backend_Model_Config_Structure_Element_Field', array(), array(), '', false
        );
        $this->_groupMock = $this->getMock(
            'Mage_Backend_Model_Config_Structure_Element_Group', array(), array(), '', false
        );
        $this->_model = new Mage_Backend_Model_Config_Structure_Element_Iterator_Field(
            $this->_groupMock, $this->_fieldMock
        );
        $this->_model->setElements(array(
            'someGroup_1' => array(
                '_elementType' => 'group',
            ),
            'someField_1' => array(
                '_elementType' => 'field',
            ),
            'someGroup_2' => array(
                '_elementType' => 'group',
            ),
            'someField_2' => array(
                '_elementType' => 'field',
            )
        ));
    }

    protected function tearDown()
    {
        unset($this->_fieldMock);
        unset($this->_groupMock);
        unset($this->_model);
    }

    public function testIteratorInitializesCorrespondingFlyweights()
    {
        $this->_fieldMock->expects($this->exactly(2))->method('setData')->with(array('_elementType' => 'field'));
        $this->_fieldMock->expects($this->any())->method('isVisible')->will($this->returnValue(true));
        $this->_groupMock->expects($this->exactly(2))->method('setData')->with(array('_elementType' => 'group'));
        $this->_groupMock->expects($this->any())->method('isVisible')->will($this->returnValue(true));
        $items = array();
        foreach ($this->_model as $item) {
            $items[] = $item;
        }
        $this->assertEquals($this->_groupMock, $items[0]);
        $this->assertEquals($this->_fieldMock, $items[1]);
        $this->assertEquals($this->_groupMock, $items[2]);
        $this->assertEquals($this->_fieldMock, $items[3]);
    }
}

