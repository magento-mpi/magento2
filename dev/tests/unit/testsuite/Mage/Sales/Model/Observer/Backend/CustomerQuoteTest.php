<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

class Mage_Sales_Model_Observer_Backend_CustomerQuoteTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Sales_Model_Observer_Backend_CustomerQuote
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_quoteMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_observerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_customerMock;

    public function setUp()
    {
        $this->_quoteMock = $this->getMock('Mage_Sales_Model_Quote',
            array('setWebsite', 'loadByCustomer', 'getId', 'setCustomerGroupId', 'collectTotals'), array(), '', false
        );
        $this->_observerMock = $this->getMock('Magento_Event_Observer', array(), array(), '', false);
        $this->_storeManagerMock = $this->getMock('Mage_Core_Model_StoreManager', array(), array(), '', false);
        $this->_configMock = $this->getMock('Mage_Customer_Model_Config_Share', array(), array(), '', false);
        $this->_eventMock = $this->getMock('Magento_Event', array('getCustomer'), array(), '', false);
        $this->_customerMock = $this->getMock('Mage_Customer_Model_Customer', array(), array(), '', false);
        $this->_observerMock->expects($this->any())->method('getEvent')->will($this->returnValue($this->_eventMock));
        $this->_eventMock
            ->expects($this->once())
            ->method('getCustomer')
            ->will($this->returnValue($this->_customerMock));
        $this->_model = new Mage_Sales_Model_Observer_Backend_CustomerQuote(
            $this->_storeManagerMock,
            $this->_configMock,
            $this->_quoteMock
        );
    }

    public function testDispatchIfCustomerDataEqual()
    {
        $this->_customerMock->expects($this->once())->method('getGroupId')->will($this->returnValue(1));
        $this->_customerMock->expects($this->once())->method('getOrigData')->will($this->returnValue(1));
        $this->_configMock->expects($this->never())->method('isWebsiteScope');
        $this->_model->dispatch($this->_observerMock);
    }

    public function testDispatchIfWebsiteScopeEnable()
    {
        $this->_customerMock->expects($this->once())->method('getGroupId')->will($this->returnValue(1));
        $this->_customerMock->expects($this->once())->method('getOrigData')->will($this->returnValue(2));
        $this->_configMock->expects($this->any())->method('isWebsiteScope')->will($this->returnValue(true));
        $this->_customerMock->expects($this->any())->method('getWebsiteId');
        $this->_storeManagerMock->expects($this->never())->method('getWebsites');
        $this->_model->dispatch($this->_observerMock);
    }

    public function testDispatchIfWebsiteScopeDisable()
    {
        $websiteMock = $this->getMock('Mage_Core_Model_Website', array(), array(), '', false);
        $this->_customerMock->expects($this->once())->method('getGroupId')->will($this->returnValue(1));
        $this->_customerMock->expects($this->once())->method('getOrigData')->will($this->returnValue(2));
        $this->_configMock->expects($this->any())->method('isWebsiteScope')->will($this->returnValue(false));
        $this->_customerMock->expects($this->never())->method('getWebsiteId');
        $this->_storeManagerMock
            ->expects($this->once())
            ->method('getWebsites')
            ->will($this->returnValue(array($websiteMock)));
        $this->_storeManagerMock->expects($this->never())->method('getWebsite');
        $this->_model->dispatch($this->_observerMock);
    }

    /**
     * @dataProvider dispatchIfArrayExistDataProvider
     * @param bool $quoteId
     */
    public function testDispatchIfArrayExist($quoteId)
    {
        $websiteMock = $this->getMock('Mage_Core_Model_Website', array(), array(), '', false);
        $this->_customerMock->expects($this->any())->method('getGroupId')->will($this->returnValue(1));
        $this->_customerMock->expects($this->once())->method('getOrigData')->will($this->returnValue(2));
        $this->_configMock->expects($this->any())->method('isWebsiteScope')->will($this->returnValue(true));
        $this->_customerMock->expects($this->never())->method('getWebsiteId');
        $this->_storeManagerMock
            ->expects($this->once())
            ->method('getWebsite')
            ->will($this->returnValue(array($websiteMock)));
        $this->_quoteMock->expects($this->once())->method('setWebsite');
        $this->_quoteMock->expects($this->once())->method('loadByCustomer')->with($this->_customerMock);
        $this->_quoteMock->expects(($this->once()))->method('getId')->will($this->returnValue($quoteId));
        $this->_quoteMock->expects($this->any())->method('save');
        $this->_model->dispatch($this->_observerMock);
    }

    public function dispatchIfArrayExistDataProvider()
    {
        return array(
            array(true),
            array(false),
        );
    }
}
