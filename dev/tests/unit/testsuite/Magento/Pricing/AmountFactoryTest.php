<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class AmountFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Pricing\AmountFactory */
    protected $amountFactory;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $objectManagerMock;

    /** @var \Magento\Pricing\AdjustmentComposite|\PHPUnit_Framework_MockObject_MockObject */
    protected $adjustmentComposite;

    /** @var \Magento\Pricing\Object\SaleableInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $saleableItem;

    /**
     * @var float
     */
    protected $amount = 5.5;

    /**
     * @var array
     */
    protected $args = [];

    protected function setUp()
    {
        $this->objectManagerMock = $this->getMock('Magento\ObjectManager');
        $this->adjustmentComposite = $this->getMock('Magento\Pricing\AdjustmentComposite', [], [], '', false);
        $this->saleableItem = $this->getMock('Magento\Pricing\Object\SaleableInterface', [], [], '', false);
        $this->args = [
            'adjustmentComposite' => $this->adjustmentComposite,
            'saleableItem' => $this->saleableItem,
            'amount' => $this->amount
        ];

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->amountFactory = $this->objectManagerHelper->getObject(
            'Magento\Pricing\AmountFactory',
            [
                'objectManager' => $this->objectManagerMock
            ]
        );
    }

    public function testCreate()
    {
        $result = $this->getMock('\Magento\Pricing\AmountInterface', [], [], '', false);
        $this->prepareObjectManagerMock($result);
        $this->assertSame(
            $result,
            $this->amountFactory->create($this->adjustmentComposite, $this->saleableItem, $this->amount)
        );
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage stdClass doesn't implement \Magento\Pricing\AmountInterface
     */
    public function testCreateException()
    {
        $this->prepareObjectManagerMock(new \stdClass());
        $this->amountFactory->create($this->adjustmentComposite, $this->saleableItem, $this->amount);
    }

    /**
     * @param object $result
     */
    protected function prepareObjectManagerMock($result)
    {
        $this->objectManagerMock
            ->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento\Pricing\Amount'), $this->equalTo($this->args))
            ->will($this->returnValue($result));
    }
}
