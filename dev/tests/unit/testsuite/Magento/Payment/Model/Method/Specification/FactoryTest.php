<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Model\Method\Specification;

/**
 * Factory Test
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \Magento\Payment\Model\Method\Specification\Factory
     */
    protected $factory;

    protected function setUp()
    {
        $this->objectManagerMock = $this->getMock('Magento\ObjectManager', array(), array(), '', false);

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->factory = $objectManagerHelper->getObject('Magento\Payment\Model\Method\Specification\Factory', array(
            'objectManager' => $this->objectManagerMock,
        ));
    }

    public function testCreateMethod()
    {
        $className = 'Magento\Payment\Model\Method\SpecificationInterface';
        $methodMock = $this->getMock($className);
        $this->objectManagerMock->expects($this->once())->method('get')->with($className)
            ->will($this->returnValue($methodMock));

        $this->assertEquals($methodMock, $this->factory->create($className));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Specification must implement SpecificationInterface
     */
    public function testWrongTypeException()
    {
        $className = 'WrongClass';
        $methodMock = $this->getMock($className);
        $this->objectManagerMock->expects($this->once())->method('get')->with($className)
            ->will($this->returnValue($methodMock));

        $this->factory->create($className);
    }
}
