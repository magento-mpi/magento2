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

class Mage_Backend_Model_Config_Structure_Element_FlyweightFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Config_Structure_Element_FlyweightFactory
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager_Zend', array(), array(), '', false);
        $this->_model = new Mage_Backend_Model_Config_Structure_Element_FlyweightFactory($this->_objectManagerMock);
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_objectManagerMock);
    }

    public function testCreate()
    {
        $this->_objectManagerMock->expects($this->any())->method('create')->will($this->returnValueMap(array(
            array('Mage_Backend_Model_Config_Structure_Element_Section', array(), true, 'sectionObject'),
            array('Mage_Backend_Model_Config_Structure_Element_Group', array(), true, 'groupObject'),
            array('Mage_Backend_Model_Config_Structure_Element_Field', array(), true, 'fieldObject'),
        )));
        $this->assertEquals('sectionObject', $this->_model->create('section'));
        $this->assertEquals('groupObject', $this->_model->create('group'));
        $this->assertEquals('fieldObject', $this->_model->create('field'));
    }
}
