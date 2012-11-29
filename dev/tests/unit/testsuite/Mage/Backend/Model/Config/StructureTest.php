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

class Mage_Backend_Model_Config_StructureTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Config_Structure
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_flyweightPoolMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_tabIteratorMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_readerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_scopeDefinerMock;

    /**
     * @var array
     */
    protected $_structureData;

    public function setUp()
    {
        $this->_flyweightPoolMock = $this->getMock(
            'Mage_Backend_Model_Config_Structure_Element_FlyweightPool', array(), array(), '', false
        );
        $this->_tabIteratorMock = $this->getMock(
            'Mage_Backend_Model_Config_Structure_Element_Iterator_Tab', array(), array(), '', false
        );
        $this->_readerMock = $this->getMock(
            'Mage_Backend_Model_Config_Structure_Reader', array(), array(), '', false
        );
        $this->_scopeDefinerMock = $this->getMock(
            'Mage_Backend_Model_Config_ScopeDefiner', array(), array(), '', false
        );

        $filePath = dirname(__DIR__) . '/_files';
        $this->_structureData = require $filePath . '/converted_config.php';
        $this->_readerMock->expects($this->once())->method('getData')
            ->will($this->returnValue($this->_structureData['config']['system'])
        );
        $this->_model = new Mage_Backend_Model_Config_Structure(
            $this->_readerMock, $this->_tabIteratorMock, $this->_flyweightPoolMock, $this->_scopeDefinerMock
        );
    }

    public function testGetTabsBuildsSectionTree()
    {
        $this->_readerMock = $this->getMock(
            'Mage_Backend_Model_Config_Structure_Reader', array(), array(), '', false
        );
        $this->_readerMock->expects($this->any())->method('getData')->will($this->returnValue(
            array('sections' => array('section1' => array('tab' => 'tab1')), 'tabs' => array('tab1' => array()))
        ));
        $expected = array('tab1' => array('children' => array('section1' => array('tab' => 'tab1'))));
        $model = new Mage_Backend_Model_Config_Structure(
            $this->_readerMock, $this->_tabIteratorMock, $this->_flyweightPoolMock, $this->_scopeDefinerMock
        );
        $this->_tabIteratorMock->expects($this->once())->method('setElements')->with($expected);
        $this->assertEquals($this->_tabIteratorMock, $model->getTabs());
    }

    public function testGetElementReturnsProperElementByPath()
    {
        $section = $this->_structureData['config']['system']['sections']['section_1'];
        $fields = $section['children']['group_level_1']['children'];
        $this->_flyweightPoolMock->expects($this->once())->method('getFlyweight')
            ->with($fields['field_3'])
            ->will($this->returnValue('expected'));
        $this->assertEquals('expected', $this->_model->getElement('section_1/group_2/field_3'));
    }

    public function testGetElementReturnsNullIfNotExistingElementIsRequested()
    {
        $this->_flyweightPoolMock->expects($this->never())->method('getFlyweight');
        $this->assertNull($this->_model->getElement('section_1/group_2/nonexisting_field'));
    }
}
