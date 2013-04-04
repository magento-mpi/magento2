<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Saas_Saas_Model_Limitation_Specification_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var Saas_Saas_Model_Limitation_Specification_Factory
     */
    protected $_modelSpecificationFactory;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_modelSpecificationFactory = $objectManagerHelper->getObject(
            'Saas_Saas_Model_Limitation_Specification_Factory',
            array(
                'objectManager' => $this->_objectManagerMock,
            )
        );
    }

    public function testCreate()
    {
        $specificationMock = $this->getMock('Saas_Saas_Model_Limitation_SpecificationInterface');
        $this->_objectManagerMock->expects($this->once())->method('get')->with('SpecificationName')
            ->will($this->returnValue($specificationMock));

        $this->assertEquals($specificationMock, $this->_modelSpecificationFactory->create('SpecificationName'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionIfNonSpecificationInterfaceCreated()
    {
        $specificationMock = $this->getMock('Some_Class');
        $this->_objectManagerMock->expects($this->once())->method('get')->with('SpecificationName')
            ->will($this->returnValue($specificationMock));

        $this->assertEquals($specificationMock, $this->_modelSpecificationFactory->create('SpecificationName'));
    }
}
