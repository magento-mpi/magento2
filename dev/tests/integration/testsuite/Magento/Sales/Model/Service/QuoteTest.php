<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Service;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Customer\Service\V1\Dto\CustomerBuilder;
use Magento\Customer\Service\V1\Dto\AddressBuilder;
use Magento\Customer\Service\V1\Dto\Region;
use Magento\Customer\Service\V1\Dto\Customer as CustomerDto;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Customer\Service\V1\CustomerAddressServiceInterface;

/**
 * @magentoAppArea adminhtml
 */
class QuoteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Service\Quote
     */
    protected $_serviceQuote;

    /**
     * @var CustomerBuilder
     */
    private $_customerBuilder;

    /**
     * @var CustomerAccountServiceInterface
     */
    protected $_customerAccountService;

    /**
     * @var CustomerAddressServiceInterface
     */
    protected $_customerAddressService;

    /**
     * @var AddressBuilder
     */
    protected $_addressBuilder;


    public function setUp()
    {
        $this->_addressBuilder = Bootstrap::getObjectManager()->get(
            'Magento\Customer\Service\V1\Dto\AddressBuilder'
        );
        $this->_customerBuilder = Bootstrap::getObjectManager()->get(
            'Magento\Customer\Service\V1\Dto\CustomerBuilder'
        );
        $this->_customerAccountService = Bootstrap::getObjectManager()->get(
            'Magento\Customer\Service\V1\CustomerAccountService'
        );
        $this->_customerAddressService = Bootstrap::getObjectManager()->get(
            'Magento\Customer\Service\V1\CustomerAddressService'
        );
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/quote.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSubmitGuestOrder()
    {
        $this->_prepareQuote(true);
        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->_serviceQuote->submitOrderWithDto();
        //Makes sure that the customer for guest checkout is not saved
        $this->assertNull($order->getCustomerId());
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/quote.php
     * @expectedException \Magento\Exception\InputException
     * @expectedExceptionMessage One or more input exceptions have occurred.
     */
    public function testSubmitOrderInvalidCustomerData()
    {
        $this->_prepareQuote(false);
        /** @var $order \Magento\Sales\Model\Order */
        $this->_serviceQuote->submitOrderWithDto();
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/quote.php
     */
    public function testSubmitOrderExistingCustomer()
    {
        $this->_prepareQuote(false);

        $customerDetails = $this->_customerDetailsBuilder->setCustomer($this->getSampleCustomerEntity())
            ->setAddresses($this->getSampleAddressEntity())->create();
        $customer = $this->_customerAccountService->createAccount($customerDetails, 'password');

        $existingCustomerId = $customer->getCustomerId();
        $customer = $this->_customerBuilder->mergeDtoWithArray(
            $customer,
            [CustomerDto::EMAIL => 'new@example.com']
        );
        $addresses = $this->_customerAddressService->getAddresses($existingCustomerId);
        $this->_serviceQuote->getQuote()->setCustomerData($customer);
        $this->_serviceQuote->getQuote()->setCustomerAddressData($addresses);
        $this->_serviceQuote->submitOrderWithDto();
        $customerId = $this->_serviceQuote->getQuote()->getCustomerData()->getCustomerId();
        $this->assertNotNull($customerId);
        //Make sure no new customer is created
        $this->assertEquals($existingCustomerId, $customerId);
        $customer = $this->_customerAccountService->getCustomer($existingCustomerId);
        $this->assertEquals('new@example.com', $customer->getEmail());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Sales/_files/quote.php
     */
    public function testSubmitOrderNewCustomer()
    {
        $this->_prepareQuote(false);
        $this->_serviceQuote->getQuote()->setCustomerData($this->getSampleCustomerEntity());
        $this->_serviceQuote->getQuote()->setCustomerAddressData($this->getSampleAddressEntity());
        $this->_serviceQuote->submitOrderWithDto();
        $customerId = $this->_serviceQuote->getQuote()->getCustomerData()->getCustomerId();
        $this->assertNotNull($customerId);
        foreach ($this->_serviceQuote->getQuote()->getCustomerAddressData() as $address) {
            $this->assertNotNull($address->getId());
            $this->assertEquals($customerId, $address->getCustomerId());
        }
    }

    /**
     * @magentoAppArea adminhtml
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Sales/_files/quote.php
     */
    public function testSubmitOrderRollbackNewCustomer()
    {
        $this->_prepareQuoteWithMockTransaction();
        $this->_serviceQuote->getQuote()->setCustomerData($this->getSampleCustomerEntity());
        $this->_serviceQuote->getQuote()->setCustomerAddressData($this->getSampleAddressEntity());
        try {
            $this->_serviceQuote->submitOrderWithDto();
        } catch (\Exception $e) {
            $this->assertEquals('submitorder exception', $e->getMessage());
        }
        $this->assertNull($this->_serviceQuote->getQuote()->getCustomerData()->getCustomerId());
    }

    /**
     * @magentoAppArea adminhtml
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Sales/_files/quote.php
     */
    public function testSubmitOrderRollbackExistingCustomer()
    {
        $this->_prepareQuoteWithMockTransaction();

        $customerDetails = $this->_customerDetailsBuilder->setCustomer($this->getSampleCustomerEntity())
            ->setAddresses($this->getSampleAddressEntity())->create();
        $customer = $this->_customerAccountService->createAccount($customerDetails, 'password');

        $existingCustomerId = $customer->getCustomerId();
        $customer = $this->_customerBuilder->mergeDtoWithArray(
            $customer,
            [CustomerDto::EMAIL => 'new@example.com']
        );
        $addresses = $this->_customerAddressService->getAddresses($existingCustomerId);
        $this->_serviceQuote->getQuote()->setCustomerData($customer);
        $this->_serviceQuote->getQuote()->setCustomerAddressData($addresses);
        try {
            $this->_serviceQuote->submitOrderWithDto();
        } catch (\Exception $e) {
            $this->assertEquals('submitorder exception', $e->getMessage());
        }
        $this->assertEquals('email@example.com', $this->_customerAccountService->getCustomer($existingCustomerId)->getEmail());
    }

    /**
     * Function to setup Quote for order
     *
     * @param bool $customerIsGuest
     */
    private function _prepareQuote($customerIsGuest)
    {
        $quoteFixture = $this->_prepareQuoteFixture($customerIsGuest);
        $this->_serviceQuote = Bootstrap::getObjectManager()->create(
            'Magento\Sales\Model\Service\Quote',
            array('quote' => $quoteFixture)
        );
    }

    /**
     * Prepare quote data
     *
     * @param bool $customerIsGuest
     * @return \Magento\Sales\Model\Quote
     */
    private function _prepareQuoteFixture($customerIsGuest)
    {
        $method = 'freeshipping_freeshipping';
        /** @var $quoteFixture \Magento\Sales\Model\Quote */
        $quoteFixture = Bootstrap::getObjectManager()->create('Magento\Sales\Model\Quote');
        $quoteFixture->load('test01', 'reserved_order_id');
        $rate = Bootstrap::getObjectManager()->create('Magento\Sales\Model\Quote\Address\Rate');
        $rate->setCode($method);
        $quoteFixture->getShippingAddress()->addShippingRate($rate);
        $quoteFixture->getShippingAddress()->setShippingMethod($method);
        $quoteFixture->setCustomerIsGuest($customerIsGuest);
        return $quoteFixture;
    }

    /**
     * Sample customer data
     *
     * @return CustomerDto
     */
    private function getSampleCustomerEntity()
    {
        $email = 'email@example.com';
        $storeId = 1;
        $firstname = 'Tester';
        $lastname = 'McTest';
        $groupId = 1;

        $this->_customerBuilder->setStoreId($storeId)
            ->setEmail($email)
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setGroupId($groupId);
        return $this->_customerBuilder->create();
    }

    /**
     * Sample Address data
     *
     * @return array
     */
    private function getSampleAddressEntity()
    {
        $this->_addressBuilder
            ->setCountryId('US')
            ->setDefaultBilling(true)
            ->setDefaultShipping(true)
            ->setPostcode('75477')
            ->setRegion(
                new Region([
                    'region_code' => 'AL',
                    'region' => 'Alabama',
                    'region_id' => 1
                ])
            )
            ->setStreet(['Green str, 67'])
            ->setTelephone('3468676')
            ->setCity('CityM')
            ->setFirstname('John')
            ->setLastname('Smith');
        $address1 = $this->_addressBuilder->create();

        $this->_addressBuilder
            ->setCountryId('US')
            ->setDefaultBilling(false)
            ->setDefaultShipping(false)
            ->setPostcode('47676')
            ->setRegion(
                new Region([
                    'region_code' => 'AL',
                    'region' => 'Alabama',
                    'region_id' => 1
                ])
            )
            ->setStreet(['Black str, 48'])
            ->setCity('CityX')
            ->setTelephone('3234676')
            ->setFirstname('John')
            ->setLastname('Smith');
        $address2 = $this->_addressBuilder->create();

        return [$address1, $address2];
    }

    /**
     * Setup $this->_serviceQuote with mock transaction object
     */
    private function _prepareQuoteWithMockTransaction()
    {
        $mockTransactionFactory = $this->getMockBuilder('\Magento\Core\Model\Resource\TransactionFactory')
            ->disableOriginalConstructor()->setMethods(['create'])->getMock();
        $mockTransaction = $this->getMockBuilder('\Magento\Core\Model\Resource\TransactionFactory')
            ->disableOriginalConstructor()->setMethods(['addObject', 'addCommitCallback', 'save'])->getMock();

        $mockTransactionFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($mockTransaction));

        $mockTransaction->expects($this->any())
            ->method('addObject');
        $mockTransaction->expects($this->any())
            ->method('addCommitCallback');
        $mockTransaction->expects($this->once())
            ->method('save')
            ->will($this->throwException(new \Exception('submitorder exception')));

        $quoteFixture = $this->_prepareQuoteFixture(false);
        $this->_serviceQuote = Bootstrap::getObjectManager()->create(
            '\Magento\Sales\Model\Service\Quote',
            array('quote' => $quoteFixture, 'transactionFactory' => $mockTransactionFactory)
        );
    }
} 
