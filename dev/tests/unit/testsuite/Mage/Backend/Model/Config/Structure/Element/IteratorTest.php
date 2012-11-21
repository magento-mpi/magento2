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

class Mage_Backend_Model_Config_Structure_Element_IteratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Config_Structure_Element_Iterator
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_flyweightMock;

    protected function setUp()
    {
        $elementData = array(
            'group1' => array(
                'id' => 1
            ),
            'group2' => array(
                'id' => 2
            )
        );
        $this->_flyweightMock = $this->getMock(
            'Mage_Backend_Model_Config_Structure_Element_Group', array(), array(), '', false
        );

        $this->_model = new Mage_Backend_Model_Config_Structure_Element_Iterator($this->_flyweightMock);
        $this->_model->setElements($elementData);
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_flyweightMock);
    }

    public function testIteratorInitializesFlyweight()
    {
        $this->_flyweightMock->expects($this->at(0))->method('setData')->with(array('id' => 1));
        $this->_flyweightMock->expects($this->at(2))->method('setData')->with(array('id' => 2));
        $this->_flyweightMock->expects($this->any())->method('isVisible')->will($this->returnValue(true));
        $counter = 0;
        foreach ($this->_model as $item) {
            $this->assertEquals($this->_flyweightMock, $item);
            $counter++;
        }
        $this->assertEquals(2, $counter);
    }

    public function testIteratorSkipsNonValidElements()
    {
        $this->_flyweightMock->expects($this->exactly(2))->method('isVisible')->will($this->returnValue(false));
        $this->_flyweightMock->expects($this->exactly(2))->method('setData');
        foreach ($this->_model as $item) {
            unset($item);
            $this->fail('Iterator shows non visible fields');
        }
    }
}
