<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\SalesRule\Model\Observer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $model;

    /**
     * @var \Magento\SalesRule\Model\Coupon|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $couponMock;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleFactory;

    /**
     * @var
     */
    protected $ruleCustomerFactory;

    /**
     * @var \Magento\Framework\Locale\Resolver|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeResolver;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->couponMock = $this->getMock(
            '\Magento\SalesRule\Model\Coupon',
            [
                '__wakeup',
                'save',
                'load',
                'getId',
                'setTimesUsed',
                'getTimesUsed',
                'updateCustomerCouponTimesUsed'
            ],
            [],
            '',
            false
        );
        $this->ruleFactory = $this->getMock('Magento\SalesRule\Model\RuleFactory', ['create'], [], '', false);
        $this->ruleCustomerFactory = $this->getMock(
            'Magento\SalesRule\Model\Rule\CustomerFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->localeResolver = $this->getMock('Magento\Framework\Locale\Resolver', [], [], '', false);

        $this->model = $helper->getObject(
            'Magento\SalesRule\Model\Observer',
            [
                'coupon' => $this->couponMock,
                'ruleFactory' => $this->ruleFactory,
                'ruleCustomerFactory' => $this->ruleCustomerFactory,
                'localeResolver' => $this->localeResolver
            ]
        );
    }

    /**
     * @param \\PHPUnit_Framework_MockObject_MockObject $observer
     * @return \PHPUnit_Framework_MockObject_MockObject $order
     */
    protected function initOrderFromEvent($observer)
    {
        $event = $this->getMock('Magento\Framework\Event', ['getOrder'], [], '', false);
        $order = $this->getMock(
            'Magento\Sales\Model\Order',
            ['getAppliedRuleIds', 'getCustomerId', 'getDiscountAmount', '__wakeup'],
            [],
            '',
            false
        );

        $observer->expects($this->once())
            ->method('getEvent')
            ->will($this->returnValue($event));
        $event->expects($this->once())
            ->method('getOrder')
            ->will($this->returnValue($order));

        return $order;
    }

    public function testSalesOrderAfterPlaceWithoutOrder()
    {
        $observer = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);
        $this->initOrderFromEvent($observer);

        $this->assertEquals($this->model, $this->model->salesOrderAfterPlace($observer));
    }

    public function testSalesOrderAfterPlaceWithoutRuleId()
    {
        $observer = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);
        $order = $this->initOrderFromEvent($observer);
        $discountAmount = 10;
        $order->expects($this->once())
            ->method('getDiscountAmount')
            ->will($this->returnValue($discountAmount));

        $this->ruleFactory->expects($this->never())
            ->method('create');
        $this->assertEquals($this->model, $this->model->salesOrderAfterPlace($observer));
    }

    /**
     * @param int|bool $ruleCustomerId
     * @dataProvider salesOrderAfterPlaceDataProvider
     */
    public function testSalesOrderAfterPlace($ruleCustomerId)
    {
        $observer = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);
        $rule = $this->getMock('Magento\SalesRule\Model\Rule', [], [], '', false);
        $ruleCustomer = $this->getMock(
            'Magento\SalesRule\Model\Rule\Customer',
            [
                'setCustomerId',
                'loadByCustomerRule',
                'getId',
                'setTimesUsed',
                'setRuleId',
                'save',
                '__wakeup'
            ],
            [],
            '',
            false
        );
        $order = $this->initOrderFromEvent($observer);
        $ruleId = 1;
        $couponId = 1;
        $customerId = 1;
        $discountAmount = 10;

        $order->expects($this->once())
            ->method('getAppliedRuleIds')
            ->will($this->returnValue($ruleId));
        $order->expects($this->once())
            ->method('getDiscountAmount')
            ->will($this->returnValue($discountAmount));
        $order->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue($customerId));
        $this->ruleFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($rule));
        $rule->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($ruleId));
        $this->ruleCustomerFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($ruleCustomer));
        $ruleCustomer->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($ruleCustomerId));
        $ruleCustomer->expects($this->once())
            ->method('setCustomerId')
            ->will($this->returnSelf());
        $ruleCustomer->expects($this->once())
            ->method('setRuleId')
            ->will($this->returnSelf());
        $this->couponMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($couponId));

        $this->couponMock->expects($this->once())
            ->method('updateCustomerCouponTimesUsed')
            ->with($customerId, $couponId);

        $this->assertEquals($this->model, $this->model->salesOrderAfterPlace($observer));
    }

    public function salesOrderAfterPlaceDataProvider()
    {
        return [
            'With customer rule id' => [1],
            'Without customer rule id' => [null]
        ];
    }
}
