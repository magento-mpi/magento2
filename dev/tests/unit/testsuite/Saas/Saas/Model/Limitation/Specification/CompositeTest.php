<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Saas_Saas_Model_Limitation_Specification_CompositeTest extends PHPUnit_Framework_TestCase
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
    protected $_modelSpecificationComposite;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Magento_Core_Controller_Request_Http', array(), array(), '', false);
        $this->_modelSpecificationFirstMock = $this->getMock('Saas_Saas_Model_Limitation_SpecificationInterface');
        $this->_modelSpecificationSecondMock = $this->getMock('Saas_Saas_Model_Limitation_SpecificationInterface');

        $this->_modelSpecificationFactoryMock = $this->getMock('Saas_Saas_Model_Limitation_Specification_Factory',
            array(), array(), '', false);
        $this->_modelSpecificationFactoryMock->expects($this->at(0))->method('create')
            ->with('modelSpecificationFirstClassName')->will($this->returnValue($this->_modelSpecificationFirstMock));
        $this->_modelSpecificationFactoryMock->expects($this->at(1))->method('create')
            ->with('modelSpecificationSecondClassName')->will($this->returnValue($this->_modelSpecificationSecondMock));

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_modelSpecificationComposite = $objectManagerHelper->getObject(
            'Saas_Saas_Model_Limitation_Specification_Composite',
            array(
                'specificationFactory' => $this->_modelSpecificationFactoryMock,
                'specificationNames' => array('modelSpecificationFirstClassName', 'modelSpecificationSecondClassName'),
            )
        );
    }

    /**
     * @param bool $isSatisfiedByFirst
     * @param bool $isSatisfiedBySecond
     * @param bool $result
     * @dataProvider dataProviderForIsSatisfiedBy
     */
    public function testIsSatisfiedBy($isSatisfiedByFirst, $isSatisfiedBySecond, $result)
    {
        $this->_modelSpecificationFirstMock->expects($this->once())->method('isSatisfiedBy')->with($this->_requestMock)
            ->will($this->returnValue($isSatisfiedByFirst));
        $this->_modelSpecificationSecondMock->expects($this->once())->method('isSatisfiedBy')->with($this->_requestMock)
            ->will($this->returnValue($isSatisfiedBySecond));

        $this->assertEquals($result, $this->_modelSpecificationComposite->isSatisfiedBy($this->_requestMock));
    }

    /**
     * @return array
     */
    public function dataProviderForIsSatisfiedBy()
    {
        return array(
            array(true, true, true),
            array(true, false, false),
        );
    }

    public function testBreakIfIsNotAllowed()
    {
        $this->_modelSpecificationFirstMock->expects($this->once())->method('isSatisfiedBy')->with($this->_requestMock)
            ->will($this->returnValue(false));
        $this->_modelSpecificationSecondMock->expects($this->never())->method('isSatisfiedBy');

        $this->assertFalse($this->_modelSpecificationComposite->isSatisfiedBy($this->_requestMock));
    }
}
