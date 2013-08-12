<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Design_Model_Limitation_Specification_Backend_ThemesTest extends PHPUnit_Framework_TestCase
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
            'Saas_Design_Model_Limitation_Specification_Backend_Themes'
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
            array('Magento_Theme_Adminhtml', 'unknown', 'unknown'),
            array('Magento_Theme_Adminhtml', 'system_design_theme', 'unknown'),
            array('Magento_Theme_Adminhtml', 'unknown', 'index'),
            array('unknown', 'system_design_theme', 'unknown'),
            array('unknown', 'system_design_theme', 'index'),
            array('unknown', 'unknown', 'new'),
            array('unknown', 'unknown', 'unknown'),
        );
    }

    /**
     * @param string $action
     * @dataProvider dataProviderForIsNotSatisfied
     */
    public function testIsNotSatisfied($action)
    {
        $this->_requestMock->expects($this->any())->method('getControllerModule')
            ->will($this->returnValue('Magento_Theme_Adminhtml'));
        $this->_requestMock->expects($this->any())->method('getControllerName')
            ->will($this->returnValue('system_design_theme'));
        $this->_requestMock->expects($this->any())->method('getActionName')
            ->will($this->returnValue($action));

        $this->assertFalse($this->_modelSpecification->isSatisfiedBy($this->_requestMock));
    }

    /**
     * @return array
     */
    public function dataProviderForIsNotSatisfied()
    {
        return array(
            array('index'),
            array('new'),
            array('grid'),
            array('edit'),
        );
    }
}
