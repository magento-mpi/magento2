<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Pricing\Price;

/**
 * Test class for \Magento\Framework\Pricing\Factory
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
        $this->model = $objectManager->getObject('Magento\Framework\Pricing\Price\Factory', array(
            'objectManager' => $this->objectManagerMock
        ));
    }

    public function testCreate()
    {
        $quantity = 2.2;
        $className = 'Magento\Framework\Pricing\Price\PriceInterface';
        $priceMock = $this->getMock($className);
        $salableItem = $this->getMock('Magento\Framework\Pricing\Object\SaleableInterface');
        $arguments = [];

        $argumentsResult = array_merge($arguments, ['salableItem' => $salableItem, 'quantity' => $quantity]);

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with($className, $argumentsResult)
            ->will($this->returnValue($priceMock));

        $this->assertEquals($priceMock, $this->model->create($salableItem, $className, $quantity, $arguments));
    }

    public function testCreateWithException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            sprintf(
                'Magento\Framework\Pricing\PriceInfo\Base doesn\'t implement %s',
                '\Magento\Framework\Pricing\Price\PriceInterface'
            )
        );
        $quantity = 2.2;
        $className = 'Magento\Framework\Pricing\PriceInfo\Base';
        $priceMock = $this->getMockBuilder($className)->disableOriginalConstructor()->getMock();
        $salableItem = $this->getMock('Magento\Framework\Pricing\Object\SaleableInterface');
        $arguments = [];

        $argumentsResult = array_merge($arguments, ['salableItem' => $salableItem, 'quantity' => $quantity]);

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with($className, $argumentsResult)
            ->will($this->returnValue($priceMock));

        $this->model->create($salableItem, $className, $quantity, $arguments);
    }
}
