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
            ),
            'group3' => array(
                'id' => 3
            )
        );
        $this->_flyweightMock = $this->getMock(
            'Mage_Backend_Model_Config_Structure_Element_Group', array(), array(), '', false
        );

        $this->_model = new Mage_Backend_Model_Config_Structure_Element_Iterator($this->_flyweightMock);
        $this->_model->setElements($elementData, 'scope');
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_flyweightMock);
    }

    public function testIteratorInitializesFlyweight()
    {
        $this->_flyweightMock->expects($this->at(0))->method('setData')->with(array('id' => 1), 'scope');
        $this->_flyweightMock->expects($this->at(2))->method('setData')->with(array('id' => 2), 'scope');
        $this->_flyweightMock->expects($this->at(4))->method('setData')->with(array('id' => 3), 'scope');
        $this->_flyweightMock->expects($this->any())->method('isVisible')->will($this->returnValue(true));
        $counter = 0;
        foreach ($this->_model as $item) {
            $this->assertEquals($this->_flyweightMock, $item);
            $counter++;
        }
        $this->assertEquals(3, $counter);
    }

    public function testIteratorSkipsNonValidElements()
    {
        $this->_flyweightMock->expects($this->exactly(3))->method('isVisible')->will($this->returnValue(false));
        $this->_flyweightMock->expects($this->exactly(3))->method('setData');
        foreach ($this->_model as $item) {
            unset($item);
            $this->fail('Iterator shows non visible fields');
        }
    }

    /**
     * @param string $id
     * @param bool $result
     * @dataProvider isLastDataProvider
     */
    public function testIsLast($id, $result)
    {
        $elementMock = $this->getMock('Mage_Backend_Model_Config_Structure_Element_Field', array(), array(), '', false);
        $elementMock->expects($this->once())->method('getId')->will($this->returnValue($id));
        $this->assertEquals($result, $this->_model->isLast($elementMock));
    }

    public function isLastDataProvider()
    {
        return array(
            array(1, false),
            array(2, false),
            array(3, true)
        );
    }
}
