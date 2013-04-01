<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Customer_Model_Limitation_Specification_Backend_CustomerOnlineTest extends PHPUnit_Framework_TestCase
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
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_modelSpecification = $objectManagerHelper->getObject(
            'Saas_Customer_Model_Limitation_Specification_Backend_CustomerOnline'
        );
    }

    /**
     * @param string $module
     * @param string $controller
     * @dataProvider dataProviderForIsAllowed
     */
    public function testIsAllowed($module, $controller)
    {
        $this->_requestMock->expects($this->any())->method('getControllerModule')->will($this->returnValue($module));
        $this->_requestMock->expects($this->any())->method('getControllerName')->will($this->returnValue($controller));

        $this->assertTrue($this->_modelSpecification->isAllowed($this->_requestMock));
    }

    /**
     * @return array
     */
    public function dataProviderForIsAllowed()
    {
        return array(
            array('Mage_Adminhtml', 'unknown'),
            array('unknown', 'customer_online'),
            array('unknown', 'unknown'),
        );
    }

    public function testIsNotAllowed()
    {
        $this->_requestMock->expects($this->any())->method('getControllerModule')
            ->will($this->returnValue('Mage_Adminhtml'));
        $this->_requestMock->expects($this->any())->method('getControllerName')
            ->will($this->returnValue('customer_online'));

        $this->assertFalse($this->_modelSpecification->isAllowed($this->_requestMock));
    }
}
