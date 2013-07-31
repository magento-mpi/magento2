<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Sales_Model_Limitation_Specification_Backend_RecurringTest extends PHPUnit_Framework_TestCase
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
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http');

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_modelSpecification = $objectManagerHelper->getObject(
            'Saas_Sales_Model_Limitation_Specification_Backend_Recurring'
        );
    }

    /**
     * @param string $module
     * @param string $controller
     * @dataProvider dataProviderForIsSatisfiedBy
     */
    public function testIsSatisfiedBy($module, $controller)
    {
        $this->_requestMock->expects($this->any())->method('getControllerModule')->will($this->returnValue($module));
        $this->_requestMock->expects($this->any())->method('getControllerName')->will($this->returnValue($controller));

        $this->assertTrue($this->_modelSpecification->isSatisfiedBy($this->_requestMock));
    }

    /**
     * @return array
     */
    public function dataProviderForIsSatisfiedBy()
    {
        return array(
            array('unknown', 'Magento_Adminhtml'),
            array('sales_recurring_profile', 'unknown'),
            array('unknown', 'unknown'),
        );
    }

    public function testIsNotAllowed()
    {
        $this->_requestMock->expects($this->any())->method('getControllerModule')
            ->will($this->returnValue('Magento_Adminhtml'));
        $this->_requestMock->expects($this->any())->method('getControllerName')
            ->will($this->returnValue('sales_recurring_profile'));

        $this->assertFalse($this->_modelSpecification->isSatisfiedBy($this->_requestMock));
    }
}
