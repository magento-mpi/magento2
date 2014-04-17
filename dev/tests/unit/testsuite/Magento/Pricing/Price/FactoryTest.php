<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\Price;

/**
 * Test class for \Magento\Pricing\Factory
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Factory
     */
    protected $model;

    /**
     * @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    public function setUp()
    {
        $this->objectManagerMock = $this->getMockBuilder('Magento\Framework\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject('Magento\Pricing\Price\Factory', array(
            'objectManager' => $this->objectManagerMock
        ));
    }

    public function testCreate()
    {
        $quantity = 2.2;
        $className = 'Magento\Pricing\Price\PriceInterface';
        $priceMock = $this->getMock($className);
        $salableItem = $this->getMock('Magento\Pricing\Object\SaleableInterface');
        $arguments = [];

        $argumentsResult = array_merge($arguments, ['salableItem' => $salableItem, 'quantity' => $quantity]);

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with($className, $argumentsResult)
            ->will($this->returnValue($priceMock));

        $this->assertEquals($priceMock, $this->model->create($salableItem, $className, $quantity, $arguments));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Magento\Pricing\PriceInfo\Base doesn't implement \Magento\Pricing\Price\PriceInterface
     */
    public function testCreateWithException()
    {
        $quantity = 2.2;
        $className = 'Magento\Pricing\PriceInfo\Base';
        $priceMock = $this->getMockBuilder($className)->disableOriginalConstructor()->getMock();
        $salableItem = $this->getMock('Magento\Pricing\Object\SaleableInterface');
        $arguments = [];

        $argumentsResult = array_merge($arguments, ['salableItem' => $salableItem, 'quantity' => $quantity]);

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with($className, $argumentsResult)
            ->will($this->returnValue($priceMock));

        $this->model->create($salableItem, $className, $quantity, $arguments);
    }
}
