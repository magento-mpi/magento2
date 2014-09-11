<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test \Magento\CustomerBalance\Block\Adminhtml\Sales\Order\Create\PaymentTest
 */
namespace Magento\CustomerBalance\Block\Adminhtml\Sales\Order\Create;

class PaymentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tested class
     *
     * @var string
     */
    protected $_className;

    /**
     * @var \Magento\CustomerBalance\Model\BalanceFactory
     */
    protected $_balanceFactoryMock;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $_sessionQuoteMock;

    /**
     * @var \Magento\Sales\Model\AdminOrder\Create
     */
    protected $_orderCreateMock;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManagerMock;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_helperMock;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_balanceInstance;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeMock;

    /**
     * initialize arguments for construct
     */
    public function setUp()
    {
        $this->_balanceInstance = $this->getMock(
            'Magento\CustomerBalance\Model\Balance',
            array('setCustomerId', 'setWebsiteId', 'getAmount', 'loadByCustomer', '__wakeup'),
            array(),
            '',
            false
        );
        $this->_balanceFactoryMock = $this->getMock(
            'Magento\CustomerBalance\Model\BalanceFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->_balanceFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_balanceInstance)
        );
        $this->_balanceInstance->expects(
            $this->any()
        )->method(
            'setCustomerId'
        )->will(
            $this->returnValue($this->_balanceInstance)
        );
        $this->_balanceInstance->expects(
            $this->any()
        )->method(
            'setWebsiteId'
        )->will(
            $this->returnValue($this->_balanceInstance)
        );
        $this->_balanceInstance->expects(
            $this->any()
        )->method(
            'loadByCustomer'
        )->will(
            $this->returnValue($this->_balanceInstance)
        );
        $this->_sessionQuoteMock = $this->getMock('Magento\Backend\Model\Session\Quote', array(), array(), '', false);
        $this->_orderCreateMock = $this->getMock('Magento\Sales\Model\AdminOrder\Create', array(), array(), '', false);
        $this->_storeManagerMock = $this->getMock(
            'Magento\Framework\StoreManagerInterface',
            array(),
            array(),
            '',
            false
        );

        $quoteMock = $this->getMock(
            'Magento\Sales\Model\Quote',
            array('getCustomerId', 'getStoreId', '__wakeup'),
            array(),
            '',
            false
        );
        $this->_orderCreateMock->expects($this->any())->method('getQuote')->will($this->returnValue($quoteMock));
        $quoteMock->expects($this->any())->method('getCustomerId')->will($this->returnValue(true));
        $quoteMock->expects($this->any())->method('getStoreId')->will($this->returnValue(true));
        $this->_helperMock = $this->getMock('Magento\CustomerBalance\Helper\Data', array(), array(), '', false);

        $this->_storeMock = $this->getMock('Magento\Store\Model\Store', array(), array(), '', false);
        $this->_storeManagerMock->expects(
            $this->any()
        )->method(
            'getStore'
        )->will(
            $this->returnValue($this->_storeMock)
        );

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_className = $helper->getObject(
            'Magento\CustomerBalance\Block\Adminhtml\Sales\Order\Create\Payment',
            array(
                'storeManager' => $this->_storeManagerMock,
                'sessionQuote' => $this->_sessionQuoteMock,
                'orderCreate' => $this->_orderCreateMock,
                'balanceFactory' => $this->_balanceFactoryMock,
                'customerBalanceHelper' => $this->_helperMock
            )
        );
    }

    /**
     * Test \Magento\CustomerBalance\Block\Adminhtml\Sales\Order\Create\Payment::getBalance()
     * Check case when customer balance is disabled
     */
    public function testGetBalanceNotEnabled()
    {
        $this->_helperMock->expects($this->once())->method('isEnabled')->will($this->returnValue(false));

        $result = $this->_className->getBalance();
        $this->assertEquals(0.0, $result);
    }

    /**
     * Test \Magento\CustomerBalance\Block\Adminhtml\Sales\Order\Create\Payment::getBalance()
     * Test if need to use converting price by current currency rate
     */
    public function testGetBalanceConvertPrice()
    {
        $this->_helperMock->expects($this->once())->method('isEnabled')->will($this->returnValue(true));
        $amount = rand(1, 100);
        $convertedAmount = $amount * 2;

        $this->_balanceInstance->expects($this->once())->method('getAmount')->will($this->returnValue($amount));
        $this->_storeMock->expects(
            $this->once()
        )->method(
            'convertPrice'
        )->with(
            $this->equalTo($amount)
        )->will(
            $this->returnValue($convertedAmount)
        );
        $result = $this->_className->getBalance(true);
        $this->assertEquals($convertedAmount, $result);
    }

    /**
     * Test \Magento\CustomerBalance\Block\Adminhtml\Sales\Order\Create\Payment::getBalance()
     * No additional cases, standard behaviour
     */
    public function testGetBalanceAmount()
    {
        $amount = rand(1, 1000);
        $this->_helperMock->expects($this->once())->method('isEnabled')->will($this->returnValue(true));
        $this->_balanceInstance->expects($this->once())->method('getAmount')->will($this->returnValue($amount));
        $result = $this->_className->getBalance();
        $this->assertEquals($amount, $result);
    }
}
