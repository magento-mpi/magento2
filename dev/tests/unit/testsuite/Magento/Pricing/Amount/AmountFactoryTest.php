<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\Amount;

/**
 * Class AmountFactoryTest
 */
class AmountFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Pricing\Amount\AmountFactory
     */
    protected $factory;

    /**
     * @var \Magento\App\ObjectManager |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \Magento\Pricing\Amount\Base|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $amountMock;

    /**
     * Test setUp
     */
    public function setUp()
    {
        $this->objectManagerMock = $this->getMock('Magento\App\ObjectManager', [], [], '', false);
        $this->amountMock = $this->getMock('Magento\Pricing\Amount\Base', [], [], '', false);
        $this->factory = new \Magento\Pricing\Amount\AmountFactory($this->objectManagerMock);
    }

    /**
     * Test method create
     */
    public function testCreate()
    {
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('Magento\Pricing\Amount\AmountInterface'),
                $this->equalTo(
                    [
                        'amount' => 'this-is-float-amount',
                        'adjustmentAmounts' => ['this-is-array-of-adjustments']
                    ]
                )
            )
            ->will($this->returnValue($this->amountMock));
        $this->assertEquals(
            $this->amountMock,
            $this->factory->create('this-is-float-amount', ['this-is-array-of-adjustments'])
        );
    }

    /**
     * Test method create
     *
     * @expectedException \InvalidArgumentException
     */
    public function testCreateException()
    {
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('Magento\Pricing\Amount\AmountInterface'),
                $this->equalTo(
                    [
                        'amount' => 'this-is-float-amount',
                        'adjustmentAmounts' => ['this-is-array-of-adjustments']
                    ]
                )
            )
            ->will($this->returnValue(new \stdClass));
        $this->assertEquals(
            $this->amountMock,
            $this->factory->create('this-is-float-amount', ['this-is-array-of-adjustments'])
        );
    }
}
