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

class Magento_Backend_Model_Config_Structure_Element_FlyweightFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Model_Config_Structure_Element_FlyweightFactory
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');
        $this->_model = new Magento_Backend_Model_Config_Structure_Element_FlyweightFactory($this->_objectManagerMock);
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_objectManagerMock);
    }

    public function testCreate()
    {
        $this->_objectManagerMock->expects($this->any())->method('create')->will($this->returnValueMap(array(
            array('Magento_Backend_Model_Config_Structure_Element_Section', array(), 'sectionObject'),
            array('Magento_Backend_Model_Config_Structure_Element_Group', array(), 'groupObject'),
            array('Magento_Backend_Model_Config_Structure_Element_Field', array(), 'fieldObject'),
        )));
        $this->assertEquals('sectionObject', $this->_model->create('section'));
        $this->assertEquals('groupObject', $this->_model->create('group'));
        $this->assertEquals('fieldObject', $this->_model->create('field'));
    }
}
