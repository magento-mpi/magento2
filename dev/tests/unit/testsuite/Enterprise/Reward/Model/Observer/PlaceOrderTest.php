<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

class Enterprise_Reward_Model_Observer_PlaceOrderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Reward_Model_Observer_PlaceOrder
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_restrictionMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modelFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_validatorMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_observerMock;

    public function setUp()
    {
        $this->_restrictionMock = $this->getMock('Enterprise_Reward_Model_Observer_PlaceOrder_RestrictionInterface');
        $this->_storeManagerMock = $this->getMock('Magento_Core_Model_StoreManager', array(), array(), '', false);
        $this->_modelFactoryMock
            = $this->getMock('Enterprise_Reward_Model_RewardFactory', array('create'), array(), '', false);
        $this->_resourceFactoryMock
            = $this->getMock('Enterprise_Reward_Model_Resource_RewardFactory', array('create'), array(), '', false);
        $this->_validatorMock
            = $this->getMock('Enterprise_Reward_Model_Reward_Balance_Validator', array(), array(), '', false);

        $this->_observerMock = $this->getMock('Magento_Event_Observer', array(), array(), '', false);

        $this->_model = new Enterprise_Reward_Model_Observer_PlaceOrder(
            $this->_restrictionMock,
            $this->_storeManagerMock,
            $this->_modelFactoryMock,
            $this->_resourceFactoryMock,
            $this->_validatorMock);
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
        $order = $this->getMock('Magento_Sales_Model_Order', array('getBaseRewardCurrencyAmount'), array(), '', false);
        $event = $this->getMock('Magento_Event', array('getOrder'), array(), '', false);
        $this->_observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getOrder')->will($this->returnValue($order));
        $order->expects($this->once())
            ->method('getBaseRewardCurrencyAmount', 'getCustomerId')->will($this->returnValue(1));
        $model = $this->getMock('Enterprise_Reward_Model_Reward', array(), array(), '', false);
        $this->_modelFactoryMock->expects($this->once())->method('create')->will($this->returnValue($model));
        $store = $this->getMock('Magento_Core_Model_Store', array(), array(), '', false);
        $this->_storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($store));
        $store->expects($this->once())->method('getWebsiteId');
        $resource = $this->getMock('Enterprise_Reward_Model_Resource_Reward', array(), array(), '', false);
        $this->_resourceFactoryMock->expects($this->once())->method('create')->will($this->returnValue($resource));
        $resource->expects($this->once())->method('getRewardSalesrule')->will($this->returnValue(array()));
        $order->expects($this->never())->method('setRewardSalesrulePoints');
        $this->_model->dispatch($this->_observerMock);
    }

    public function testDispatchIfRewardCurrencyAmountBelowNull()
    {
        $this->_restrictionMock->expects($this->once())->method('isAllowed')->will($this->returnValue(true));
        $order = $this->getMock('Magento_Sales_Model_Order', array('getBaseRewardCurrencyAmount'), array(), '', false);
        $event = $this->getMock('Magento_Event', array('getOrder'), array(), '', false);
        $this->_observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getOrder')->will($this->returnValue($order));
        $order->expects($this->once())->method('getBaseRewardCurrencyAmount')->will($this->returnValue(-1));
        $resource = $this->getMock('Enterprise_Reward_Model_Resource_Reward', array(), array(), '', false);
        $this->_resourceFactoryMock->expects($this->once())->method('create')->will($this->returnValue($resource));
        $resource->expects($this->once())->method('getRewardSalesrule')->will($this->returnValue(array()));
        $this->_model->dispatch($this->_observerMock);
    }

    public function testDispatchIfArrayIsNotEmpty()
    {
        $data = array('key1' => array('points_delta' => 60), 'key2' => array('points_delta' => 45));
        $this->_restrictionMock->expects($this->once())->method('isAllowed')->will($this->returnValue(true));
        $order = $this->getMock('Magento_Sales_Model_Order',
            array('getBaseRewardCurrencyAmount', 'setRewardSalesrulePoints'), array(), '', false
        );
        $event = $this->getMock('Magento_Event', array('getOrder'), array(), '', false);
        $this->_observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getOrder')->will($this->returnValue($order));
        $order->expects($this->once())->method('getBaseRewardCurrencyAmount')->will($this->returnValue(-1));
        $resource = $this->getMock('Enterprise_Reward_Model_Resource_Reward', array(), array(), '', false);
        $this->_resourceFactoryMock->expects($this->once())->method('create')->will($this->returnValue($resource));
        $resource->expects($this->once())->method('getRewardSalesrule')->will($this->returnValue($data));
        $order->expects($this->once())->method('setRewardSalesrulePoints')->with(105);
        $this->_model->dispatch($this->_observerMock);
    }
}
