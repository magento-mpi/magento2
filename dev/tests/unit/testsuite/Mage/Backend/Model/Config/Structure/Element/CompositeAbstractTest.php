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

class Mage_Backend_Model_Config_Structure_Element_CompositeAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Config_Structure_Element_CompositeAbstract
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_iteratorMock;

    public function setUp()
    {
        $this->_iteratorMock = $this->getMock(
            'Mage_Backend_Model_Config_Structure_Element_Iterator', array(), array(), '', false
        );
        $this->_model = $this->getMockForAbstractClass(
            'Mage_Backend_Model_Config_Structure_Element_CompositeAbstract',
            array($this->_iteratorMock)
        );
        $this->_model->setData(array(
            'id' => 'elementId',
            'label' => 'Element Label',
            'customAttribute' => 'Custom attribute value',
            'children' => array(
                'someGroup' => array()
            )
        ));
    }

    protected function tearDown()
    {
        unset($this->_iteratorMock);
        unset($this->_model);
    }

    public function testGetChildrenInitializesFlyweight()
    {
        $this->_iteratorMock->expects($this->once())->method('setElements')->with(array('someGroup' => array()));
        $this->assertEquals($this->_iteratorMock, $this->_model->getChildren());
    }

    public function testHasChildrenReturnsFalseIfThereAreNoChildren()
    {
        $this->_iteratorMock->expects($this->once())->method('setElements')->with(array('someGroup' => array()));
        $this->assertFalse($this->_model->hasChildren());
    }

    public function testHasChildrenReturnsTrueIfThereAreVisibleChildren()
    {
        $this->_iteratorMock->expects($this->once())->method('current')->will($this->returnValue(true));
        $this->_iteratorMock->expects($this->at(2))->method('valid')->will($this->returnValue(true));
        $this->assertTrue($this->_model->hasChildren());
    }
}
