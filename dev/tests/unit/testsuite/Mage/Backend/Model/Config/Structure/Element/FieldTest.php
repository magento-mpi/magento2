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

class Mage_Backend_Model_Config_Structure_Element_FieldTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Config_Structure_Element_Field
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryHelperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_applicationMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_authorizationMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_backendFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_structureMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_iteratorMock;

    /**
     * Test element data
     *
     * @var array
     */
    protected $_testData = array(
        'id' => 'elementId',
        'label' => 'Element Label',
        'customAttribute' => 'Custom attribute value',
        'children' => array(
            'someGroup' => array()
        )
    );

    public function setUp()
    {
        $this->_iteratorMock = $this->getMock(
            'Mage_Backend_Model_Config_Structure_Element_Iterator', array(), array(), '', false
        );
        $this->_factoryHelperMock = $this->getMock('Mage_Core_Model_Factory_Helper', array(), array(), '', false);
        $this->_applicationMock = $this->getMock('Mage_Core_Model_App', array(), array(), '', false);
        $this->_authorizationMock = $this->getMock('Mage_Core_Model_Authorization', array(), array(), '', false);
        $this->_backendFactoryMock = $this->getMock(
            'Mage_Backend_Model_Config_Backend_Factory', array(), array(), '', false
        );
        $this->_structureMock = $this->getMock(
            'Mage_Backend_Model_Config_Backend_Factory', array(), array(), '', false
        );

        $this->_model = new Mage_Backend_Model_Config_Structure_Element_Field(
            $this->_factoryHelperMock,
            $this->_applicationMock,
            $this->_authorizationMock,
            $this->_backendFactoryMock,
            $this->_structureMock
        );
    }

    protected function tearDown()
    {
        unset($this->_iteratorMock);
        unset($this->_applicationMock);
        unset($this->_authorizationMock);
        unset($this->_backendFactoryMock);
        unset($this->_structureMock);
        unset($this->_factoryHelperMock);
        unset($this->_model);
    }

    public function testSetDataInitializesChildIterator()
    {
        $this->_iteratorMock->expects($this->once())->method('setElements')
            ->with(array('someGroup' => array()), 'scope');
        $this->_model->setData($this->_testData, 'scope');
    }

    public function testHasChildrenReturnsFalseIfThereAreNoChildren()
    {
        $this->assertFalse($this->_model->hasChildren());
    }

    public function testHasChildrenReturnsTrueIfThereAreVisibleChildren()
    {
        $this->_iteratorMock->expects($this->once())->method('current')->will($this->returnValue(true));
        $this->_iteratorMock->expects($this->once())->method('valid')->will($this->returnValue(true));
        $this->assertTrue($this->_model->hasChildren());
    }
}
