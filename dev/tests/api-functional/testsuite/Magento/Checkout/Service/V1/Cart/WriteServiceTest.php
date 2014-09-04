<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Cart;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

class WriteServiceTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'checkoutCartWriteServiceV1';
    const RESOURCE_PATH = '/V1/carts/';

    protected $createdQuotes = [];

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    public function testCreate()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => RestConfig::HTTP_METHOD_POST,
            ),
            'soap' => array(
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Create',
            ),
        );

        $quoteId = $this->_webApiCall($serviceInfo);
        $this->assertGreaterThan(0, $quoteId);
        $this->createdQuotes[] = $quoteId;
    }

    public function tearDown()
    {
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->objectManager->create('\Magento\Sales\Model\Quote');
        foreach ($this->createdQuotes as $quoteId) {
            $quote->load($quoteId);
            $quote->delete();
        }
    }

    /**
     * @magentoApiDataFixture Magento/Sales/_files/quote.php
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     */
    public function testAssignCustomer()
    {
        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = $this->objectManager->create('\Magento\Sales\Model\Quote')->load('test01', 'reserved_order_id');
        $cartId = $quote->getId();
        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = $this->objectManager->create('\Magento\Customer\Model\Customer')->load(1);
        $customerId = $customer->getId();

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/carts/' . $cartId,
                'httpMethod' => RestConfig::HTTP_METHOD_PUT,
            ),
            'soap' => array(
                'service' => 'checkoutCartWriteServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'checkoutCartWriteServiceV1AssignCustomer',
            ),
        );

        $requestData = array(
            'cartId' => $cartId,
            'customerId' => $customerId,
        );
        // Cart must be anonymous (see fixture)
        $this->assertEmpty($quote->getCustomerId());

        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));
        // Reload target quote
        $quote = $this->objectManager->create('\Magento\Sales\Model\Quote')->load('test01', 'reserved_order_id');
        $this->assertEquals(0, $quote->getCustomerIsGuest());
        $this->assertEquals($customer->getId(), $quote->getCustomerId());
        $this->assertEquals($customer->getFirstname(), $quote->getCustomerFirstname());
        $this->assertEquals($customer->getLastname(), $quote->getCustomerLastname());

    }

    /**
     * @magentoApiDataFixture Magento/Sales/_files/quote.php
     * @expectedException \Exception
     */
    public function testAssignCustomerThrowsExceptionIfThereIsNoCustomerWithGivenId()
    {
        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = $this->objectManager->create('\Magento\Sales\Model\Quote')->load('test01', 'reserved_order_id');
        $cartId = $quote->getId();
        $customerId = 9999;
        $serviceInfo = array(
            'soap' => array(
                'serviceVersion' => 'V1',
                'service' => 'checkoutCartWriteServiceV1',
                'operation' => 'checkoutCartWriteServiceV1AssignCustomer',
            ),
            'rest' => array(
                'resourcePath' => '/V1/carts/' . $cartId,
                'httpMethod' => RestConfig::HTTP_METHOD_PUT,
            ),
        );
        $requestData = array(
            'cartId' => $cartId,
            'customerId' => $customerId,
        );

        $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     * @expectedException \Exception
     */
    public function testAssignCustomerThrowsExceptionIfThereIsNoCartWithGivenId()
    {
        $cartId = 9999;
        $customerId = 1;
        $serviceInfo = array(
            'soap' => array(
                'service' => 'checkoutCartWriteServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'checkoutCartWriteServiceV1AssignCustomer',
            ),
            'rest' => array(
                'resourcePath' => '/V1/carts/' . $cartId,
                'httpMethod' => RestConfig::HTTP_METHOD_PUT,
            ),
        );
        $requestData = array(
            'cartId' => $cartId,
            'customerId' => $customerId,
        );

        $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * @magentoApiDataFixture Magento/Sales/_files/quote_with_customer.php
     * @expectedException \Exception
     * @expectedExceptionMessage Cannot assign customer to the given cart. The cart is not anonymous.
     */
    public function testAssignCustomerThrowsExceptionIfTargetCartIsNotAnonymous()
    {
        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = $this->objectManager->create('\Magento\Customer\Model\Customer')->load(1);
        $customerId = $customer->getId();
        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = $this->objectManager->create('\Magento\Sales\Model\Quote')->load('test01', 'reserved_order_id');
        $cartId = $quote->getId();

        $serviceInfo = array(
            'rest' => array(
                'httpMethod' => RestConfig::HTTP_METHOD_PUT,
                'resourcePath' => '/V1/carts/' . $cartId,
            ),
            'soap' => array(
                'service' => 'checkoutCartWriteServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'checkoutCartWriteServiceV1AssignCustomer',
            ),
        );

        $requestData = array(
            'cartId' => $cartId,
            'customerId' => $customerId,
        );
        $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * @magentoApiDataFixture Magento/Sales/_files/quote.php
     * @magentoApiDataFixture Magento/Customer/_files/customer_non_default_website_id.php
     * @expectedException \Exception
     * @expectedExceptionMessage Cannot assign customer to the given cart. The cart belongs to different store.
     */
    public function testAssignCustomerThrowsExceptionIfCartIsAssignedToDifferentStore()
    {
        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = $this->objectManager->create('\Magento\Customer\Model\Customer')->load(1);
        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = $this->objectManager->create('\Magento\Sales\Model\Quote')->load('test01', 'reserved_order_id');

        $customerId = $customer->getId();
        $cartId = $quote->getId();

        $serviceInfo = array(
            'soap' => array(
                'service' => 'checkoutCartWriteServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'checkoutCartWriteServiceV1AssignCustomer',
            ),
            'rest' => array(
                'httpMethod' => RestConfig::HTTP_METHOD_PUT,
                'resourcePath' => '/V1/carts/' . $cartId,
            ),
        );

        $requestData = array(
            'cartId' => $cartId,
            'customerId' => $customerId,
        );
        $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_address_saved.php
     * @magentoApiDataFixture Magento/Sales/_files/quote.php
     * @expectedException \Exception
     * @expectedExceptionMessage Cannot assign customer to the given cart. Customer already has active cart.
     */
    public function testAssignCustomerThrowsExceptionIfCustomerAlreadyHasActiveCart()
    {
        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = $this->objectManager->create('\Magento\Customer\Model\Customer')->load(1);
        // Customer has a quote with reserved order ID test_order_1 (see fixture)
        /** @var $customerQuote \Magento\Sales\Model\Quote */
        $customerQuote = $this->objectManager->create('\Magento\Sales\Model\Quote')
            ->load('test_order_1', 'reserved_order_id');
        $customerQuote->setIsActive(1)->save();
        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = $this->objectManager->create('\Magento\Sales\Model\Quote')->load('test01', 'reserved_order_id');

        $cartId = $quote->getId();
        $customerId = $customer->getId();

        $serviceInfo = array(
            'soap' => array(
                'service' => 'checkoutCartWriteServiceV1',
                'operation' => 'checkoutCartWriteServiceV1AssignCustomer',
                'serviceVersion' => 'V1',
            ),
            'rest' => array(
                'resourcePath' => '/V1/carts/' . $cartId,
                'httpMethod' => RestConfig::HTTP_METHOD_PUT,
            ),
        );

        $requestData = array(
            'cartId' => $cartId,
            'customerId' => $customerId,
        );
        $this->_webApiCall($serviceInfo, $requestData);
    }
}
