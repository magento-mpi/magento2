<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test \Magento\CustomerBalance\Block\Adminhtml\Sales\Order\Create\PaymentTest
 */
class Magento_CustomerBalance_Block_Adminhtml_Sales_Order_Create_PaymentTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tested class
     *
     * @var string
     */
    protected $_className = 'Magento\CustomerBalance\Block\Adminhtml\Sales\Order\Create\Payment';

    /**
     * Test \Magento\CustomerBalance\Block\Adminhtml\Sales\Order\Create\Payment::getBalance()
     * Check case when customer balance is disabled
     */
    public function testGetBalanceNotEnabled()
    {
        $helperMock = $this->getMockBuilder('Magento\CustomerBalance\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(array('isEnabled'))
            ->getMock();
        $helperMock->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(false));

        $helperFactoryMock = $this->getMock('Magento\Core\Model\Factory\Helper', array('get'), array(), '', false);
        $helperFactoryMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Magento\CustomerBalance\Helper\Data'))
            ->will($this->returnValue($helperMock));

        $coreDataMock = $this->_getCoreDataMock();

        $contextMock = $this->_getContextMock();
        $contextMock->expects($this->any())
            ->method('getHelperFactory')
            ->will($this->returnValue($helperFactoryMock));

        $arguments = array($coreDataMock, $contextMock);
        $objectMock = $this->getMockBuilder($this->_className)
            ->setConstructorArgs($arguments, array())
            ->setMethods(array('_getStoreManagerModel', '_getOrderCreateModel', '_getBalanceInstance'))
            ->getMock();
        $result = $objectMock->getBalance();
        $this->assertEquals(0.0, $result);
    }

    /**
     * Test \Magento\CustomerBalance\Block\Adminhtml\Sales\Order\Create\Payment::getBalance()
     * Test if need to use converting price by current currency rate
     */
    public function testGetBalanceConvertPrice()
    {
        $helperMock = $this->getMockBuilder('Magento\CustomerBalance\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(array('isEnabled'))
            ->getMock();
        $helperMock->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));

        $helperFactoryMock = $this->getMock('Magento\Core\Model\Factory\Helper', array('get'), array(), '', false);
        $helperFactoryMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Magento\CustomerBalance\Helper\Data'))
            ->will($this->returnValue($helperMock));

        $contextMock = $this->_getContextMock();
        $contextMock->expects($this->any())
            ->method('getHelperFactory')
            ->will($this->returnValue($helperFactoryMock));

        // Store Mock
        $amount = rand(1, 100);
        $convertedAmount = $amount * 2;
        $storeMock = $this->getMockBuilder('Magento\Core\Model\Store')
            ->disableOriginalConstructor()
            ->getMock();
        $storeMock->expects($this->once())
            ->method('convertPrice')
            ->with($this->equalTo($amount))
            ->will($this->returnValue($convertedAmount));

        // Store Manager
        $storeManagerMock = $this->getMockBuilder('Magento\Core\Model\StoreManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getStore'))
            ->getMock();
        $storeManagerMock->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($storeMock));

        $coreDataMock = $this->_getCoreDataMock();

        $arguments = array($coreDataMock, $contextMock);
        $objectMock = $this->getMockBuilder($this->_className)
            ->setConstructorArgs($arguments, array())
            ->setMethods(array('_getStoreManagerModel', '_getOrderCreateModel', '_getBalanceInstance'))
            ->getMock();

        $objectMock->expects($this->once())
            ->method('_getStoreManagerModel')
            ->will($this->returnValue($storeManagerMock));

        $quoteMock = new \Magento\Object(array('quote' => new \Magento\Object(array('store_id' => rand(1, 1000)))));
        $objectMock->expects($this->once())
            ->method('_getOrderCreateModel')
            ->will($this->returnValue($quoteMock));

        $objectMock->expects($this->any())
            ->method('_getBalanceInstance')
            ->will($this->returnValue(new \Magento\Object(array('amount' => $amount))));

        $result = $objectMock->getBalance(true);
        $this->assertEquals($convertedAmount, $result);
    }

    /**
     * Test \Magento\CustomerBalance\Block\Adminhtml\Sales\Order\Create\Payment::getBalance()
     * No additional cases, standard behaviour
     */
    public function testGetBalanceAmount()
    {
        $amount = rand(1, 1000);
        $helperMock = $this->getMockBuilder('Magento\CustomerBalance\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(array('isEnabled'))
            ->getMock();
        $helperMock->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));

        $helperFactoryMock = $this->getMock('Magento\Core\Model\Factory\Helper', array('get'), array(), '', false);
        $helperFactoryMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Magento\CustomerBalance\Helper\Data'))
            ->will($this->returnValue($helperMock));

        $contextMock = $this->_getContextMock();
        $contextMock->expects($this->any())
            ->method('getHelperFactory')
            ->will($this->returnValue($helperFactoryMock));

        $coreDataMock = $this->_getCoreDataMock();

        $arguments = array($coreDataMock, $contextMock);
        $objectMock = $this->getMockBuilder($this->_className)
            ->setConstructorArgs($arguments, array())
            ->setMethods(array('_getStoreManagerModel', '_getOrderCreateModel', '_getBalanceInstance'))
            ->getMock();
        $objectMock->expects($this->any())
            ->method('_getBalanceInstance')
            ->will($this->returnValue(new \Magento\Object(array('amount' => $amount))));
        $result = $objectMock->getBalance();
        $this->assertEquals($amount, $result);
    }

    /**
     * Return mock instance of \Magento\Core\Block\Template\Context object
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getContextMock()
    {
        $methods = array('getHelperFactory', 'getRequest', 'getLayout', 'getEventManager', 'getUrlBuilder',
            'getTranslator', 'getCache', 'getDesignPackage', 'getSession', 'getStoreConfig', 'getFrontController',
            'getDirs', 'getLogger', 'getFilesystem');
        return $this->getMockBuilder('Magento\Core\Block\Template\Context')
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * Return mock instance of \Magento\Core\Helper\Data object
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getCoreDataMock()
    {
        $methods = array();
        return $this->getMockBuilder('Magento\Core\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }
}
