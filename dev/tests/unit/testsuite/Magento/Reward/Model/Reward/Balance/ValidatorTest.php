<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Reward\Model\Reward\Balance;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Model\Reward\Balance\Validator
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modelFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_orderMock;

    protected function setUp()
    {
        $this->_storeManagerMock = $this->getMock('Magento\Store\Model\StoreManager', array(), array(), '', false);
        $this->_modelFactoryMock =
            $this->getMock('Magento\Reward\Model\RewardFactory', array('create'), array(), '', false);
        $this->_sessionMock = $this->getMock('Magento\Checkout\Model\Session',
            array('setUpdateSection', 'setGotoSection'), array(), '', false);
        $this->_orderMock = $this->getMock(
            'Magento\Sales\Model\Order',
            array('getRewardPointsBalance', '__wakeup'),
            array(),
            '',
            false
        );
        $this->_model = new \Magento\Reward\Model\Reward\Balance\Validator(
            $this->_storeManagerMock,
            $this->_modelFactoryMock,
            $this->_sessionMock
        );
    }

    public function testValidateWhenBalanceAboveNull()
    {
        $this->_orderMock->expects($this->any())->method('getRewardPointsBalance')->will($this->returnValue(1));
        $store = $this->getMock('Magento\Store\Model\Store', array(), array(), '', false);
        $this->_storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($store));
        $store->expects($this->once())->method('getWebsiteId');
        $reward = $this->getMock(
            'Magento\Reward\Model\Reward',
            array('getPointsBalance', '__wakeup'),
            array(),
            '',
            false
        );
        $this->_modelFactoryMock->expects($this->once())->method('create')->will($this->returnValue($reward));
        $reward->expects($this->once())->method('getPointsBalance')->will($this->returnValue(1));
        $this->_model->validate($this->_orderMock);
    }

    /**
     * @expectedException \Magento\Reward\Model\Reward\Balance\Exception
     * @expectedExceptionMessage You don't have enough reward points to pay for this purchase.
     */
    public function testValidateWhenBalanceNotEnoughToPlaceOrder()
    {
        $this->_orderMock->expects($this->any())->method('getRewardPointsBalance')->will($this->returnValue(1));
        $store = $this->getMock('Magento\Store\Model\Store', array(), array(), '', false);
        $this->_storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($store));
        $store->expects($this->once())->method('getWebsiteId');
        $reward = $this->getMock(
            'Magento\Reward\Model\Reward',
            array('getPointsBalance', '__wakeup'),
            array(),
            '',
            false
        );
        $this->_modelFactoryMock->expects($this->once())->method('create')->will($this->returnValue($reward));
        $reward->expects($this->once())->method('getPointsBalance')->will($this->returnValue(0.5));
        $this->_sessionMock->expects($this->once())->method('setUpdateSection')->with('payment-method');
        $this->_sessionMock->expects($this->once())->method('setGotoSection')->with('payment');

        $this->_model->validate($this->_orderMock);
    }
}
