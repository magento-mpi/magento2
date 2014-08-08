<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\PaymentMethod;

use Magento\Checkout\Service\V1\Data\PaymentMethod;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'checkoutPaymentMethodReadServiceV1';
    const RESOURCE_PATH = '/V1/carts/';

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_address_saved.php
     */
    public function testGetList()
    {
        /** @var \Magento\Sales\Model\Quote  $quote */
        $quote = $this->objectManager->create('Magento\Sales\Model\Quote');
        $quote->load('test_order_1', 'reserved_order_id');
        $cartId = $quote->getId();

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/payment-methods',
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
            ),
            'soap' => array(
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'getList',
            ),
        );

        $requestData = ["cartId" => $cartId];
        $requestResponce = $this->_webApiCall($serviceInfo, $requestData);

        $expectedResponce = [
            PaymentMethod::CODE => 'checkmo',
            PaymentMethod::TITLE => 'Check / Money order'
        ];

        $this->assertEquals(1, count($requestResponce));
        $this->assertEquals($expectedResponce, $requestResponce[0]);
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_payment_saved.php
     */
    public function testGetPayment()
    {
        /** @var \Magento\Sales\Model\Quote  $quote */
        $quote = $this->objectManager->create('Magento\Sales\Model\Quote');
        $quote->load('test_order_1_with_payment', 'reserved_order_id');
        $cartId = $quote->getId();

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/selected-payment-methods',
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
            ),
            'soap' => array(
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'getPayment',
            ),
        );

        $requestData = ["cartId" => $cartId];
        $requestResponce = $this->_webApiCall($serviceInfo, $requestData);

        $expectedResponce = [
            'method' => 'checkmo',
            'po_number' => 'poNumber',
            'cc_cid' => 'ccCid',
            'cc_owner' => 'tester',
            'cc_number' => '1000-2000-3000-4000',
            'cc_type' => 'visa',
            'cc_exp_year' => 2014,
            'cc_exp_month' => 1
        ];

        $this->assertEquals($expectedResponce, $requestResponce);
    }
}
 