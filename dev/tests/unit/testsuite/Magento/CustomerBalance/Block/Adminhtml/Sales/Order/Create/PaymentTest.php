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
 * Test Magento_CustomerBalance_Block_Adminhtml_Sales_Order_Create_PaymentTest
 */
class Magento_CustomerBalance_Block_Adminhtml_Sales_Order_Create_PaymentTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tested class
     *
     * @var string
     */
    protected $_className = 'Magento_CustomerBalance_Block_Adminhtml_Sales_Order_Create_Payment';

    /**
     * @var Magento_CustomerBalance_Model_BalanceFactory
     */
    protected $_balanceFactoryMock;

    /**
     * @var Magento_Adminhtml_Model_Session_Quote
     */
    protected $_sessionQuoteMock;

    /**
     * @var Magento_Adminhtml_Model_Sales_Order_Create
     */
    protected $_orderCreateMock;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManagerMock;

    /**
     * initialize arguments for construct
     */
    public function setUp()
    {
        $this->_balanceFactoryMock = $this->getMock(
            'Magento_CustomerBalance_Model_BalanceFactory', array('create'), array(), '', false
        );
        $this->_balanceFactoryMock->expects($this->any())
            ->method('create')
            ->will(
                $this->returnValue(
                    $this->getMock('Magento_CustomerBalance_Model_Balance', array(), array(), '', false)
                )
            );
        $this->_sessionQuoteMock = $this->getMock(
            'Magento_Adminhtml_Model_Session_Quote', array(), array(), '', false
        );
        $this->_orderCreateMock = $this->getMock(
            'Magento_Adminhtml_Model_Sales_Order_Create', array(), array(), '', false
        );
        $this->_storeManagerMock = $this->getMock(
            'Magento_Core_Model_StoreManagerInterface', array(), array(), '', false
        );
    }

    /**
     * Test Magento_CustomerBalance_Block_Adminhtml_Sales_Order_Create_Payment::getBalance()
     * Check case when customer balance is disabled
     */
    public function testGetBalanceNotEnabled()
    {
        $helperMock = $this->getMockBuilder('Magento_CustomerBalance_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('isEnabled'))
            ->getMock();
        $helperMock->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(false));

        $helperFactoryMock = $this->getMock('Magento_Core_Model_Factory_Helper', array('get'), array(), '', false);
        $helperFactoryMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Magento_CustomerBalance_Helper_Data'))
            ->will($this->returnValue($helperMock));

        $coreDataMock = $this->_getCoreDataMock();

        $contextMock = $this->_getContextMock();
        $contextMock->expects($this->any())
            ->method('getHelperFactory')
            ->will($this->returnValue($helperFactoryMock));

        $arguments = array(
            $this->_balanceFactoryMock,
            $this->_sessionQuoteMock,
            $this->_orderCreateMock,
            $this->_storeManagerMock,
            $coreDataMock,
            $contextMock
        );
        $objectMock = $this->getMockBuilder($this->_className)
            ->setConstructorArgs($arguments, array())
            ->setMethods(array('_getStoreManagerModel', '_getOrderCreateModel', '_getBalanceInstance'))
            ->getMock();
        $result = $objectMock->getBalance();
        $this->assertEquals(0.0, $result);
    }

    /**
     * Test Magento_CustomerBalance_Block_Adminhtml_Sales_Order_Create_Payment::getBalance()
     * Test if need to use converting price by current currency rate
     */
    public function testGetBalanceConvertPrice()
    {
        $helperMock = $this->getMockBuilder('Magento_CustomerBalance_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('isEnabled'))
            ->getMock();
        $helperMock->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));

        $helperFactoryMock = $this->getMock('Magento_Core_Model_Factory_Helper', array('get'), array(), '', false);
        $helperFactoryMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Magento_CustomerBalance_Helper_Data'))
            ->will($this->returnValue($helperMock));

        $contextMock = $this->_getContextMock();
        $contextMock->expects($this->any())
            ->method('getHelperFactory')
            ->will($this->returnValue($helperFactoryMock));

        // Store Mock
        $amount = rand(1, 100);
        $convertedAmount = $amount * 2;
        $storeMock = $this->getMockBuilder('Magento_Core_Model_Store')
            ->disableOriginalConstructor()
            ->getMock();
        $storeMock->expects($this->once())
            ->method('convertPrice')
            ->with($this->equalTo($amount))
            ->will($this->returnValue($convertedAmount));

        // Store Manager
        $storeManagerMock = $this->getMockBuilder('Magento_Core_Model_StoreManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getStore'))
            ->getMock();
        $storeManagerMock->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($storeMock));

        $coreDataMock = $this->_getCoreDataMock();

        $arguments = array(
            $this->_balanceFactoryMock,
            $this->_sessionQuoteMock,
            $this->_orderCreateMock,
            $this->_storeManagerMock,
            $coreDataMock,
            $contextMock
        );
        $objectMock = $this->getMockBuilder($this->_className)
            ->setConstructorArgs($arguments, array())
            ->setMethods(array('_getStoreManagerModel', '_getOrderCreateModel', '_getBalanceInstance'))
            ->getMock();

        $objectMock->expects($this->once())
            ->method('_getStoreManagerModel')
            ->will($this->returnValue($storeManagerMock));

        $quoteMock = new Magento_Object(array('quote' => new Magento_Object(array('store_id' => rand(1, 1000)))));
        $objectMock->expects($this->once())
            ->method('_getOrderCreateModel')
            ->will($this->returnValue($quoteMock));

        $objectMock->expects($this->any())
            ->method('_getBalanceInstance')
            ->will($this->returnValue(new Magento_Object(array('amount' => $amount))));

        $result = $objectMock->getBalance(true);
        $this->assertEquals($convertedAmount, $result);
    }

    /**
     * Test Magento_CustomerBalance_Block_Adminhtml_Sales_Order_Create_Payment::getBalance()
     * No additional cases, standard behaviour
     */
    public function testGetBalanceAmount()
    {
        $amount = rand(1, 1000);
        $helperMock = $this->getMockBuilder('Magento_CustomerBalance_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('isEnabled'))
            ->getMock();
        $helperMock->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));

        $helperFactoryMock = $this->getMock('Magento_Core_Model_Factory_Helper', array('get'), array(), '', false);
        $helperFactoryMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Magento_CustomerBalance_Helper_Data'))
            ->will($this->returnValue($helperMock));

        $contextMock = $this->_getContextMock();
        $contextMock->expects($this->any())
            ->method('getHelperFactory')
            ->will($this->returnValue($helperFactoryMock));

        $coreDataMock = $this->_getCoreDataMock();

        $arguments = array(
            $this->_balanceFactoryMock,
            $this->_sessionQuoteMock,
            $this->_orderCreateMock,
            $this->_storeManagerMock,
            $coreDataMock,
            $contextMock
        );
        $objectMock = $this->getMockBuilder($this->_className)
            ->setConstructorArgs($arguments, array())
            ->setMethods(array('_getStoreManagerModel', '_getOrderCreateModel', '_getBalanceInstance'))
            ->getMock();
        $objectMock->expects($this->any())
            ->method('_getBalanceInstance')
            ->will($this->returnValue(new Magento_Object(array('amount' => $amount))));
        $result = $objectMock->getBalance();
        $this->assertEquals($amount, $result);
    }

    /**
     * Return mock instance of Magento_Core_Block_Template_Context object
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getContextMock()
    {
        $methods = array('getHelperFactory', 'getRequest', 'getLayout', 'getEventManager', 'getUrlBuilder',
            'getTranslator', 'getCache', 'getDesignPackage', 'getSession', 'getStoreConfig', 'getFrontController',
            'getDirs', 'getLogger', 'getFilesystem');
        return $this->getMockBuilder('Magento_Core_Block_Template_Context')
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * Return mock instance of Magento_Core_Helper_Data object
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getCoreDataMock()
    {
        $methods = array();
        return $this->getMockBuilder('Magento_Core_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }
}
