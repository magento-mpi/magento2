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

class Magento_Backend_Model_Config_Structure_Element_SectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Model_Config_Structure_Element_Section
     */
    protected $_model;

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
    protected $_iteratorMock;

    protected function setUp()
    {
        $this->_iteratorMock = $this->getMock(
            'Magento_Backend_Model_Config_Structure_Element_Iterator_Field', array(), array(), '', false
        );
        $this->_applicationMock = $this->getMock('Magento_Core_Model_App', array(), array(), '', false);
        $this->_authorizationMock = $this->getMock('Magento_AuthorizationInterface');

        $this->_model = new Magento_Backend_Model_Config_Structure_Element_Section(
            $this->_applicationMock, $this->_iteratorMock, $this->_authorizationMock
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_iteratorMock);
        unset($this->_applicationMock);
        unset($this->_authorizationMock);
    }

    public function testIsAllowedReturnsFalseIfNoResourceIsSpecified()
    {
        $this->assertFalse($this->_model->isAllowed());
    }

    public function testIsAllowedReturnsTrueIfResourcesIsValidAndAllowed()
    {
        $this->_authorizationMock->expects($this->once())
            ->method('isAllowed')
            ->with('someResource')
            ->will($this->returnValue(true));

        $this->_model->setData(array('resource' => 'someResource'), 'store');
        $this->assertTrue($this->_model->isAllowed());
    }

    public function testIsVisibleFirstChecksIfSectionIsAllowed()
    {
        $this->_applicationMock->expects($this->never())->method('isSingleStoreMode');
        $this->assertFalse($this->_model->isVisible());
    }

    public function testIsVisibleProceedsWithVisibilityCheckIfSectionIsAllowed()
    {
        $this->_authorizationMock->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $this->_applicationMock->expects($this->once())->method('isSingleStoreMode')->will($this->returnValue(true));
        $this->_model->setData(array('resource' => 'Magento_Adminhtml::all'), 'scope');
        $this->_model->isVisible();
    }
}


