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
    protected $_factoryHelperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_applicationMock;

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

        $this->_model = $this->getMockForAbstractClass(
            'Mage_Backend_Model_Config_Structure_Element_CompositeAbstract',
            array($this->_factoryHelperMock, $this->_applicationMock, $this->_iteratorMock)
        );
    }

    protected function tearDown()
    {
        unset($this->_iteratorMock);
        unset($this->_applicationMock);
        unset($this->_factoryHelperMock);
        unset($this->_model);
    }

    public function testSetDataInitializesChildIterator()
    {
        $this->_iteratorMock->expects($this->once())->method('setElements')
            ->with(array('someGroup' => array()), 'scope');
        $this->_model->setData($this->_testData, 'scope');
    }

    public function testSetDataInitializesChildIteratorWithEmptyArrayIfNoChildrenArePresent()
    {
        $this->_iteratorMock->expects($this->once())->method('setElements')
            ->with(array(), 'scope');
        $this->_model->setData(array(), 'scope');
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

    public function testIsVisibleReturnsTrueIfThereAreVisibleChildren()
    {
        $this->_applicationMock->expects($this->once())->method('isSingleStoreMode')->will($this->returnValue(true));
        $this->_iteratorMock->expects($this->once())->method('current')->will($this->returnValue(true));
        $this->_iteratorMock->expects($this->once())->method('valid')->will($this->returnValue(true));
        $this->assertTrue($this->_model->isVisible());
    }

    public function testIsVisibleReturnsTrueIfElementHasFrontEndModel()
    {
        $this->_applicationMock->expects($this->once())->method('isSingleStoreMode')->will($this->returnValue(true));
        $this->_model->setData(array('frontend_model' => 'Model_Name'), 'scope');
        $this->assertTrue($this->_model->isVisible());
    }

    public function testIsVisibleReturnsFalseIfElementHasNoChildrenAndFrontendModel()
    {
        $this->_applicationMock->expects($this->once())->method('isSingleStoreMode')->will($this->returnValue(true));
        $this->assertFalse($this->_model->isVisible());
    }
}
