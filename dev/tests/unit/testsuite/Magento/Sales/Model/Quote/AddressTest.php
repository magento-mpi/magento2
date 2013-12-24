<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Quote;

/**
 * Unit tests covering the class Mage_Sales_Model_Quote_Address
 */
class AddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Default Quote address id
     */
    const QUOTE_ADDRESS_ID = 1;

    /**
     * Default customer billing address
     */
    const CUSTOMER_DEF_BILLING_ADDRESS_ID = '2';

    /**
     * Customer address ID
     */
    const CUSTOMER_ADDRESS_ID = '3';

    /**
     * Customer Address 2
     */
    const CUSTOMER_ADDRESS_ID_2 = '4';

    /**
     * Customer id
     */
    const CUSTOMER_ID = 5;

    /**
     * Quote Id
     */
    const QUOTE_ID = 'Quote_ID';

    /**
     * Fixture class name
     *
     * @var string
     */
    protected $_fixtureClassName = 'Magento\Sales\Model\Quote\AddressFixture';

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getMockAddressObject()
    {
        return $this->getMockBuilder($this->_fixtureClassName)
            ->disableOriginalConstructor()
            ->setMethods(
                array(
                    'getQuote', 'getCustomerAddress', 'getAddressType',
                    'getCustomerAddressId', 'setSameAsBilling', 'getId', '__wakeup'
                )
            )
            ->getMock();
    }

    /**
     * Test case with Same billing address and customer address
     */
    public function testSameBillingAndShippingAddress()
    {
        $addressMock = $this->_getMockAddressObject();

        $mockCustomer = $this->_getMockCustomerObject();

        $this->_setDefaultShippingForCustomer($mockCustomer, self::CUSTOMER_DEF_BILLING_ADDRESS_ID);

        $quote = $this->_mockQuoteObject($mockCustomer);

        $addressMock->expects($this->once())
            ->method('getAddressType')
            ->will($this->returnValue(\Magento\Sales\Model\Quote\Address::TYPE_SHIPPING));

        $addressMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(null));

        $addressMock->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quote));

        $addressMock->expects($this->any())
            ->method('getCustomerAddressId')
            ->will($this->returnValue(self::CUSTOMER_ADDRESS_ID));

        $addressMock->expects($this->once())
            ->method('setSameAsBilling')
            ->with($this->equalTo(self::QUOTE_ADDRESS_ID));

        $addressMock->populateBeforeSaveData();
    }

    /**
     * Test case with default billing address is false
     */
    public function testBeforeSaveWithDefaultShippingAddressFalse()
    {
        $addressMock = $this->_getMockAddressObject();

        $mockCustomer = $this->_getMockCustomerObjectWithNullDefaultShippingAddress();

        $quote = $this->_mockQuoteObject($mockCustomer);

        $addressMock->expects($this->any())
            ->method('getAddressType')
            ->will($this->returnValue(\Magento\Sales\Model\Quote\Address::TYPE_SHIPPING));

        $addressMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(null));

        $addressMock->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quote));

        $addressMock->expects($this->any())
            ->method('getCustomerAddressId')
            ->will($this->returnValue(self::CUSTOMER_ADDRESS_ID));

        $addressMock->expects($this->once())
            ->method('setSameAsBilling')
            ->with($this->equalTo(self::QUOTE_ADDRESS_ID));

        $addressMock->populateBeforeSaveData();
    }

    /**
     * With null customer address
     */
    public function testBeforeSaveWithCustomerAddressNull()
    {
        $addressMock = $this->_getMockAddressObject();

        $mockCustomer = $this->_getMockCustomerObject();

        $quote = $this->_mockQuoteObject($mockCustomer);

        $addressMock->expects($this->once())
            ->method('getAddressType')
            ->will($this->returnValue(\Magento\Sales\Model\Quote\Address::TYPE_SHIPPING));

        $addressMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(null));

        $addressMock->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quote));

        $addressMock->expects($this->any())
            ->method('getCustomerAddressId')
            ->will($this->returnValue(null));

        $addressMock->expects($this->once())
            ->method('setSameAsBilling')
            ->with($this->equalTo(self::QUOTE_ADDRESS_ID));

        $addressMock->populateBeforeSaveData();
    }

    /**
     * Test case where none of the criteria does not
     * match and we set the flag to 0
     */
    public function testBillingNotSameAsShipping()
    {
        $addressMock = $this->_getMockAddressObject();

        $mockCustomer = $this->_getMockCustomerObject();

        $this->_setDefaultShippingForCustomer($mockCustomer, self::CUSTOMER_ADDRESS_ID_2);

        $quote = $this->_mockQuoteObject($mockCustomer, false);

        $addressMock->expects($this->any())
            ->method('getAddressType')
            ->will($this->returnValue(\Magento\Sales\Model\Quote\Address::TYPE_SHIPPING));

        $addressMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(null));

        $addressMock->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quote));

        $addressMock->expects($this->any())
            ->method('getCustomerAddressId')
            ->will($this->returnValue(self::CUSTOMER_ADDRESS_ID));

        $addressMock->expects($this->once())
            ->method('setSameAsBilling')
            ->with($this->equalTo(0));

        $addressMock->populateBeforeSaveData();
    }

    /**
     * Set the default Shipping address for the customer
     *
     * @param PHPUnit_Framework_MockObject_MockObject $mockCustomer
     * @param int $id
     */
    protected function _setDefaultShippingForCustomer($mockCustomer, $id)
    {
        $customerDefaultShippingAddress = $this->getMockBuilder('Magento\Customer\Model\Address')
            ->setMethods(array('getId', '__wakeup'))
            ->disableOriginalConstructor()
            ->getMock();

        $customerDefaultShippingAddress->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($id));

        $mockCustomer->expects($this->any())
            ->method('getDefaultShippingAddress')
            ->will($this->returnValue($customerDefaultShippingAddress));
    }

    /**
     * Get the mock quote object
     *
     * @param  PHPUnit_Framework_MockObject_MockObject $mockCustomer
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _mockQuoteObject($mockCustomer)
    {
        $quote = $this->getMockBuilder('Magento\Sales\Model\Quote')
            ->disableOriginalConstructor()
            ->setMethods(array('getId', 'getCustomerId', 'getCustomer', '__wakeup'))
            ->getMock();

        $quote->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::QUOTE_ID));

        $quote->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue(self::CUSTOMER_ID));

        $quote->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue(false));

        $quote->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue($mockCustomer));
        return $quote;
    }

    /**
     * Gets the mock customer object
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getMockCustomerObject()
    {
        $mockCustomer = $this->getMockBuilder('Magento\Customer\Model\Customer')
            ->disableOriginalConstructor()
            ->setMethods(array('getDefaultBillingAddress', 'getDefaultShippingAddress', '__wakeup'))
            ->getMock();

        $customerDefaultBillingAddress = $this->getMockBuilder('Magento\Customer\Model\Address')
            ->setMethods(array('getId', '__wakeup'))
            ->disableOriginalConstructor()
            ->getMock();


        $customerDefaultBillingAddress->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::CUSTOMER_DEF_BILLING_ADDRESS_ID));


        $mockCustomer->expects($this->any())
            ->method('getDefaultBillingAddress')
            ->will($this->returnValue($customerDefaultBillingAddress));


        return $mockCustomer;
    }

    /**
     * Get customer mock without default shipping address
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getMockCustomerObjectWithNullDefaultShippingAddress()
    {
        $mockCustomer = $this->getMockBuilder('Magento\Customer\Model\Customer')
            ->disableOriginalConstructor()
            ->setMethods(array('getDefaultShippingAddress', '__wakeup'))
            ->getMock();

        $mockCustomer->expects($this->any())
            ->method('getDefaultShippingAddress')
            ->will($this->returnValue(false));
        return $mockCustomer;
    }
}
