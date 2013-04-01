<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Pci_Model_Limitation_Specification_Backend_CryptKeyTest extends PHPUnit_Framework_TestCase
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
            'Saas_Pci_Model_Limitation_Specification_Backend_CryptKey'
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
            array('Enterprise_Pci_Adminhtml', 'unknown'),
            array('unknown', 'crypt_key'),
            array('unknown', 'unknown'),
        );
    }

    public function testIsNotAllowed()
    {
        $this->_requestMock->expects($this->any())->method('getControllerModule')
            ->will($this->returnValue('Enterprise_Pci_Adminhtml'));
        $this->_requestMock->expects($this->any())->method('getControllerName')
            ->will($this->returnValue('crypt_key'));

        $this->assertFalse($this->_modelSpecification->isAllowed($this->_requestMock));
    }
}
