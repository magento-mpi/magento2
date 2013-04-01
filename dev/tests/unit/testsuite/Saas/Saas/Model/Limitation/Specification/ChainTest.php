<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Saas_Model_Limitation_Specification_ChainTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modelSpecificationFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modelSpecificationFirstMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modelSpecificationSecondMock;

    /**
     * @var Saas_Saas_Model_Limitation_SpecificationInterface
     */
    protected $_modelSpecificationChain;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
        $this->_modelSpecificationFirstMock = $this->getMock('Saas_Saas_Model_Limitation_SpecificationInterface');
        $this->_modelSpecificationSecondMock = $this->getMock('Saas_Saas_Model_Limitation_SpecificationInterface');

        $this->_modelSpecificationFactoryMock = $this->getMock('Saas_Saas_Model_Limitation_Specification_Factory',
            array(), array(), '', false);
        $this->_modelSpecificationFactoryMock->expects($this->at(0))->method('create')
            ->with('modelSpecificationFirstClassName')->will($this->returnValue($this->_modelSpecificationFirstMock));
        $this->_modelSpecificationFactoryMock->expects($this->at(1))->method('create')
            ->with('modelSpecificationSecondClassName')->will($this->returnValue($this->_modelSpecificationSecondMock));

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_modelSpecificationChain = $objectManagerHelper->getObject(
            'Saas_Saas_Model_Limitation_Specification_Chain',
            array(
                'specificationFactory' => $this->_modelSpecificationFactoryMock,
                'specificationNames' => array('modelSpecificationFirstClassName', 'modelSpecificationSecondClassName'),
            )
        );
    }

    /**
     * @param bool $isAllowedFirst
     * @param bool $isAllowedSecond
     * @param bool $result
     * @dataProvider dataProviderForIsAllowed
     */
    public function testIsAllowed($isAllowedFirst, $isAllowedSecond, $result)
    {
        $this->_modelSpecificationFirstMock->expects($this->once())->method('isAllowed')->with($this->_requestMock)
            ->will($this->returnValue($isAllowedFirst));
        $this->_modelSpecificationSecondMock->expects($this->once())->method('isAllowed')->with($this->_requestMock)
            ->will($this->returnValue($isAllowedSecond));

        $this->assertEquals($result, $this->_modelSpecificationChain->isAllowed($this->_requestMock));
    }

    /**
     * @return array
     */
    public function dataProviderForIsAllowed()
    {
        return array(
            array(true, true, true),
            array(true, false, false),
        );
    }

    public function testBreakIfIsNotAllowed()
    {
        $this->_modelSpecificationFirstMock->expects($this->once())->method('isAllowed')->with($this->_requestMock)
            ->will($this->returnValue(false));
        $this->_modelSpecificationSecondMock->expects($this->never())->method('isAllowed');

        $this->assertFalse($this->_modelSpecificationChain->isAllowed($this->_requestMock));
    }
}
