<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reward\Model\Observer;

class PlaceOrderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Model\Observer\PlaceOrder
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_restrictionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modelFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_validatorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_observerMock;

    protected function setUp()
    {
        $this->_restrictionMock = $this->getMock('Magento\Reward\Model\Observer\PlaceOrder\RestrictionInterface');
        $this->_storeManagerMock = $this->getMock('Magento\Store\Model\StoreManager', [], [], '', false);
        $this->_modelFactoryMock = $this->getMock(
            'Magento\Reward\Model\RewardFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->_resourceFactoryMock = $this->getMock(
            'Magento\Reward\Model\Resource\RewardFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->_validatorMock = $this->getMock(
            'Magento\Reward\Model\Reward\Balance\Validator',
            [],
            [],
            '',
            false
        );

        $this->_observerMock = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);

        $this->_model = new \Magento\Reward\Model\Observer\PlaceOrder(
            $this->_restrictionMock,
            $this->_storeManagerMock,
            $this->_modelFactoryMock,
            $this->_resourceFactoryMock,
            $this->_validatorMock
        );
    }

    public function testDispatchIfRestrictionNotAllowed()
    {
        $this->_restrictionMock->expects($this->once())->method('isAllowed')->will($this->returnValue(false));
        $this->_observerMock->expects($this->never())->method('getEvent');
        $this->_model->dispatch($this->_observerMock);
    }

    public function testDispatchIfRewardCurrencyAmountAboveNull()
    {
        $this->_restrictionMock->expects($this->once())->method('isAllowed')->will($this->returnValue(true));
        $order = $this->getMock(
            'Magento\Sales\Model\Order',
            ['getBaseRewardCurrencyAmount', '__wakeup'],
            [],
            '',
            false
        );
        $event = $this->getMock('Magento\Framework\Event', ['getOrder'], [], '', false);
        $this->_observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getOrder')->will($this->returnValue($order));
        $order->expects(
            $this->once()
        )->method(
            'getBaseRewardCurrencyAmount',
            'getCustomerId'
        )->will(
            $this->returnValue(1)
        );
        $model = $this->getMock('Magento\Reward\Model\Reward', [], [], '', false);
        $this->_modelFactoryMock->expects($this->once())->method('create')->will($this->returnValue($model));
        $store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $this->_storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($store));
        $store->expects($this->once())->method('getWebsiteId');
        $resource = $this->getMock('Magento\Reward\Model\Resource\Reward', [], [], '', false);
        $this->_resourceFactoryMock->expects($this->once())->method('create')->will($this->returnValue($resource));
        $resource->expects($this->once())->method('getRewardSalesrule')->will($this->returnValue([]));
        $order->expects($this->never())->method('setRewardSalesrulePoints');
        $this->_model->dispatch($this->_observerMock);
    }

    public function testDispatchIfRewardCurrencyAmountBelowNull()
    {
        $this->_restrictionMock->expects($this->once())->method('isAllowed')->will($this->returnValue(true));
        $order = $this->getMock(
            'Magento\Sales\Model\Order',
            ['getBaseRewardCurrencyAmount', '__wakeup'],
            [],
            '',
            false
        );
        $event = $this->getMock('Magento\Framework\Event', ['getOrder'], [], '', false);
        $this->_observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getOrder')->will($this->returnValue($order));
        $order->expects($this->once())->method('getBaseRewardCurrencyAmount')->will($this->returnValue(-1));
        $resource = $this->getMock('Magento\Reward\Model\Resource\Reward', [], [], '', false);
        $this->_resourceFactoryMock->expects($this->once())->method('create')->will($this->returnValue($resource));
        $resource->expects($this->once())->method('getRewardSalesrule')->will($this->returnValue([]));
        $this->_model->dispatch($this->_observerMock);
    }

    public function testDispatchIfArrayIsNotEmpty()
    {
        $data = ['key1' => ['points_delta' => 60], 'key2' => ['points_delta' => 45]];
        $this->_restrictionMock->expects($this->once())->method('isAllowed')->will($this->returnValue(true));
        $order = $this->getMock(
            'Magento\Sales\Model\Order',
            ['getBaseRewardCurrencyAmount', 'setRewardSalesrulePoints', '__wakeup'],
            [],
            '',
            false
        );
        $event = $this->getMock('Magento\Framework\Event', ['getOrder'], [], '', false);
        $this->_observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getOrder')->will($this->returnValue($order));
        $order->expects($this->once())->method('getBaseRewardCurrencyAmount')->will($this->returnValue(-1));
        $resource = $this->getMock('Magento\Reward\Model\Resource\Reward', [], [], '', false);
        $this->_resourceFactoryMock->expects($this->once())->method('create')->will($this->returnValue($resource));
        $resource->expects($this->once())->method('getRewardSalesrule')->will($this->returnValue($data));
        $order->expects($this->once())->method('setRewardSalesrulePoints')->with(105);
        $this->_model->dispatch($this->_observerMock);
    }
}
