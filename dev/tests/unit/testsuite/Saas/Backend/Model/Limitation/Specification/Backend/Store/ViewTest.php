<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Backend_Model_Limitation_Specification_Backend_Store_ViewTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var Saas_Saas_Model_Limitation_SpecificationInterface
     */
    protected $_modelSpecification;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Magento_Core_Controller_Request_Http', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_modelSpecification = $objectManagerHelper->getObject(
            'Saas_Backend_Model_Limitation_Specification_Backend_Store_View'
        );
    }

    /**
     * @param string $module
     * @param string $controller
     * @param string $action
     * @dataProvider dataProviderForIsSatisfiedBy
     */
    public function testIsSatisfiedBy($module, $controller, $action)
    {
        $this->_requestMock->expects($this->any())->method('getControllerModule')->will($this->returnValue($module));
        $this->_requestMock->expects($this->any())->method('getControllerName')->will($this->returnValue($controller));
        $this->_requestMock->expects($this->any())->method('getActionName')->will($this->returnValue($action));

        $this->assertTrue($this->_modelSpecification->isSatisfiedBy($this->_requestMock));
    }

    /**
     * @return array
     */
    public function dataProviderForIsSatisfiedBy()
    {
        return array(
            array('Saas_Backend_Adminhtml', 'unknown', 'unknown'),
            array('Saas_Backend_Adminhtml', 'system_store', 'unknown'),
            array('Saas_Backend_Adminhtml', 'unknown', 'deleteStore'),
            array('unknown', 'system_store', 'unknown'),
            array('unknown', 'system_store', 'deleteStore'),
            array('unknown', 'unknown', 'deleteStore'),
            array('unknown', 'unknown', 'unknown'),
        );
    }

    public function testIsNotSatisfied()
    {
        $this->_requestMock->expects($this->any())->method('getControllerModule')
            ->will($this->returnValue('Saas_Backend_Adminhtml'));
        $this->_requestMock->expects($this->any())->method('getControllerName')
            ->will($this->returnValue('system_store'));
        $this->_requestMock->expects($this->any())->method('getActionName')
            ->will($this->returnValue('deleteStore'));

        $this->assertFalse($this->_modelSpecification->isSatisfiedBy($this->_requestMock));
    }
}
